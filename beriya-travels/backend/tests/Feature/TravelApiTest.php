<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Departure;
use App\Models\Offer;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_offers_include_their_destination(): void
    {
        $destination = Destination::query()->create([
            'name' => 'Saïdia',
            'slug' => 'saidia-test',
            'description' => 'Plage méditerranéenne.',
            'image_url' => 'https://example.test/saidia.jpg',
            'image_alt' => 'Plage de Saïdia',
            'is_active' => true,
        ]);
        Offer::query()->create([
            'destination_id' => $destination->id,
            'title' => 'Saïdia Express',
            'category' => 'Mer & détente',
            'summary' => 'Une journée en bord de mer.',
            'badge' => 'Une journée',
            'duration' => '1 jour',
            'includes' => 'Transport aller-retour',
            'price_mad' => 199,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->getJson('/api/offers')
            ->assertOk()
            ->assertJsonPath('0.title', 'Saïdia Express')
            ->assertJsonPath('0.destination.name', 'Saïdia');
    }

    public function test_departures_are_listed_for_the_selected_month_with_their_program(): void
    {
        $departureDate = now()->addMonth()->startOfMonth()->addDays(10);
        $destination = Destination::query()->create([
            'name' => 'Saïdia',
            'slug' => 'saidia-calendar',
            'description' => 'Plage méditerranéenne.',
            'image_url' => 'https://example.test/saidia.jpg',
            'image_alt' => 'Plage de Saïdia',
            'is_active' => true,
        ]);
        $offer = Offer::query()->create([
            'destination_id' => $destination->id,
            'title' => 'Saïdia Express',
            'category' => 'Mer & détente',
            'summary' => 'Une journée en bord de mer.',
            'badge' => 'Une journée',
            'duration' => '1 jour',
            'includes' => 'Transport aller-retour',
            'price_mad' => 199,
            'program' => [['day' => 'Matin', 'time' => '08:00', 'title' => 'Départ d’Oujda', 'detail' => 'Route vers Saïdia.']],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Departure::query()->create([
            'offer_id' => $offer->id,
            'departure_date' => $departureDate->toDateString(),
            'return_date' => $departureDate->toDateString(),
            'status' => 'open',
        ]);
        Departure::query()->create([
            'offer_id' => $offer->id,
            'departure_date' => $departureDate->copy()->addDays(7)->toDateString(),
            'return_date' => $departureDate->copy()->addDays(7)->toDateString(),
            'status' => 'cancelled',
        ]);

        $this->getJson('/api/departures?month='.$departureDate->format('Y-m'))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.departure_date', $departureDate->toDateString())
            ->assertJsonPath('0.offer.program.0.title', 'Départ d’Oujda');
    }

    public function test_seeder_keeps_current_month_departures_on_or_after_today(): void
    {
        $this->travelTo(now()->startOfMonth()->addDays(20));
        $this->seed();

        $today = today();
        $currentMonthDepartures = Departure::query()
            ->whereBetween('departure_date', [$today->toDateString(), $today->endOfMonth()->toDateString()])
            ->count();

        $this->assertSame(4, $currentMonthDepartures);
    }

    public function test_reservations_reject_invalid_details(): void
    {
        $this->postJson('/api/reservations', [
            'offer_id' => 0,
            'full_name' => 'A',
            'email' => 'not-an-email',
            'departure_date' => '2020-01-01',
            'travelers' => 0,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['offer_id', 'full_name', 'email', 'departure_date', 'travelers']);
    }

    public function test_booking_request_returns_a_reference_and_saves_an_estimated_total(): void
    {
        $destination = Destination::query()->create([
            'name' => 'Saïdia',
            'slug' => 'saidia-booking',
            'description' => 'Plage méditerranéenne.',
            'image_url' => 'https://example.test/saidia.jpg',
            'image_alt' => 'Plage de Saïdia',
            'is_active' => true,
        ]);
        $offer = Offer::query()->create([
            'destination_id' => $destination->id,
            'title' => 'Saïdia Express',
            'category' => 'Mer & détente',
            'summary' => 'Une journée en bord de mer.',
            'badge' => 'Une journée',
            'duration' => '1 jour',
            'includes' => 'Transport aller-retour',
            'price_mad' => 199,
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $departure = Departure::query()->create([
            'offer_id' => $offer->id,
            'departure_date' => now()->addDays(7)->toDateString(),
            'return_date' => now()->addDays(7)->toDateString(),
            'status' => 'open',
        ]);

        $response = $this->postJson('/api/reservations', [
            'offer_id' => $offer->id,
            'departure_id' => $departure->id,
            'full_name' => 'Samira Client',
            'email' => 'samira@example.com',
            'phone' => '+212600000000',
            'departure_date' => now()->addDays(7)->toDateString(),
            'travelers' => 3,
            'message' => 'Sièges côte à côte si possible.',
        ])
            ->assertCreated()
            ->assertJsonPath('offer_title', 'Saïdia Express')
            ->assertJsonPath('travelers', 3)
            ->assertJsonPath('price_per_traveler_mad', '199.00')
            ->assertJsonPath('estimated_total_mad', '597.00');

        $this->assertMatchesRegularExpression('/^BT-[A-F0-9-]{36}$/', $response->json('booking_reference'));
        $offer->update(['price_mad' => 250]);
        $this->assertDatabaseHas('reservations', [
            'offer_id' => $offer->id,
            'booking_reference' => $response->json('booking_reference'),
            'price_per_traveler_mad' => '199.00',
            'estimated_total_mad' => '597.00',
            'departure_id' => $departure->id,
            'travelers' => 3,
            'status' => 'pending',
        ]);
    }

    public function test_reservations_reject_inactive_offers(): void
    {
        $destination = Destination::query()->create([
            'name' => 'Saïdia',
            'slug' => 'saidia-inactive',
            'description' => 'Plage méditerranéenne.',
            'image_url' => 'https://example.test/saidia.jpg',
            'image_alt' => 'Plage de Saïdia',
            'is_active' => true,
        ]);
        $offer = Offer::query()->create([
            'destination_id' => $destination->id,
            'title' => 'Offre indisponible',
            'category' => 'Mer & détente',
            'summary' => 'Offre désactivée.',
            'badge' => 'Une journée',
            'duration' => '1 jour',
            'includes' => 'Transport aller-retour',
            'price_mad' => 199,
            'is_active' => false,
            'sort_order' => 1,
        ]);

        $this->postJson('/api/reservations', [
            'offer_id' => $offer->id,
            'full_name' => 'Samira Client',
            'email' => 'samira@example.com',
            'departure_date' => now()->addDays(7)->toDateString(),
            'travelers' => 1,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['offer_id']);

        $this->assertSame(0, Reservation::query()->count());
    }

    public function test_contact_messages_require_a_valid_body(): void
    {
        $this->postJson('/api/contact', [
            'full_name' => 'Client',
            'email' => 'client@example.com',
            'subject' => 'Question',
            'message' => 'Court',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['message']);
    }
}
