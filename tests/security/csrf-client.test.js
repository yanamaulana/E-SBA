// Run: node tests/security/csrf-client.test.js
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

let prefilter;
let submit;
const $ = {
    ajaxPrefilter: callback => { prefilter = callback; },
    param: data => new URLSearchParams(data).toString()
};
const document = {
    cookie: 'other=value; esba_csrf_cookie=current-token',
    addEventListener: (event, callback) => { submit = callback; }
};
const window = {
    jQuery: $, FormData,
    location: { href: 'https://example.test/ESBA/Dashboard', origin: 'https://example.test' },
    esbaCsrf: { name: 'esba_csrf_token', cookie: 'esba_csrf_cookie', hash: 'page-token' }
};
const context = { window, document, URL, URLSearchParams };
const source = fs.readFileSync('assets/js/csrf.js', 'utf8');
vm.runInNewContext(source, context);

const request = { type: 'POST', url: '/ESBA/approve', data: 'CBReq_No%5B%5D=A&CBReq_No%5B%5D=B&esba_csrf_token=old' };
prefilter(request);
const body = new URLSearchParams(request.data);
assert.deepEqual(body.getAll('CBReq_No[]'), ['A', 'B']);
assert.deepEqual(body.getAll('esba_csrf_token'), ['current-token']);

const upload = new FormData();
upload.append('note', 'Lampiran & pembayaran');
upload.append('esba_csrf_token', 'old');
prefilter({ type: 'POST', url: '/ESBA/upload', data: upload, processData: false });
assert.equal(upload.get('note'), 'Lampiran & pembayaran');
assert.deepEqual(upload.getAll('esba_csrf_token'), ['current-token']);

for (const url of ['https://other.test/post', '//other.test/post']) {
    const external = { type: 'POST', url, data: 'a=1' };
    prefilter(external);
    assert.equal(external.data, 'a=1', 'Never disclose CSRF tokens cross-origin');
}
const get = { type: 'GET', url: '/ESBA/report', data: 'a=1' };
prefilter(get);
assert.equal(get.data, 'a=1');
const empty = { type: 'POST', url: '/ESBA/action' };
prefilter(empty);
assert.equal(new URLSearchParams(empty.data).get('esba_csrf_token'), 'current-token');

document.cookie = 'esba_csrf_cookie=renewed-token';
prefilter(request);
assert.equal(new URLSearchParams(request.data).get('esba_csrf_token'), 'renewed-token');
const field = { value: 'old' };
submit({ target: { method: 'post', action: '/ESBA/action', elements: { namedItem: () => field } } });
assert.equal(field.value, 'renewed-token');
const installed = prefilter;
vm.runInNewContext(source, context);
assert.equal(prefilter, installed, 'Do not register duplicate handlers');
console.log('PASS: serialized arrays, uploads, same-origin scope, GET exclusion, empty POST, token renewal, native forms, duplicate registration');
