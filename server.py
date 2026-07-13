#!/usr/bin/env python3
import http.server
import socketserver

class NoCacheHandler(http.server.SimpleHTTPRequestHandler):
    def end_headers(self):
        self.send_header('Cache-Control', 'no-cache, no-store, must-revalidate')
        self.send_header('Pragma', 'no-cache')
        self.send_header('Expires', '0')
        super().end_headers()

PORT = 8080
with socketserver.TCPServer(("", PORT), NoCacheHandler) as httpd:
    print(f"Server running at http://192.168.1.7:{PORT}")
    httpd.serve_forever()
