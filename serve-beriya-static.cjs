const fs = require('node:fs');
const http = require('node:http');
const path = require('node:path');

const assetsDirectory = path.join(
  __dirname,
  'beriya-travels',
  'frontend',
  'src',
  'assets'
);
const files = new Map([
  ['/', { path: path.join(__dirname, 'website v1.html'), type: 'text/html; charset=utf-8' }],
  [
    '/beriya-travels/frontend/src/assets/beriya-wordmark.svg',
    { path: path.join(assetsDirectory, 'beriya-wordmark.svg'), type: 'image/svg+xml' },
  ],
  [
    '/beriya-travels/frontend/src/assets/beriya-signature.svg',
    { path: path.join(assetsDirectory, 'beriya-signature.svg'), type: 'image/svg+xml' },
  ],
]);

http.createServer((request, response) => {
  const file = files.get(new URL(request.url, 'http://localhost').pathname);
  if (!file) {
    response.writeHead(404);
    response.end('Not found');
    return;
  }

  fs.readFile(file.path, (error, content) => {
    if (error) {
      response.writeHead(500);
      response.end('Unable to read Beriya Travels resource');
      return;
    }

    response.writeHead(200, { 'Content-Type': file.type });
    response.end(content);
  });
}).listen(5500, '127.0.0.1', () => {
  console.log('Beriya Travels is available at http://localhost:5500/');
});
