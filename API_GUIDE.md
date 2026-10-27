# API routes and HMAC guide

This guide covers the routes in [routes/api.php](routes/api.php), the API-key middleware, and the HMAC implementation. It describes the current code and the additional steps needed to enable HMAC.

## Current status

| Feature | Current behavior |
| --- | --- |
| API routes | 58 routes, all with unique Laravel names. |
| API key | Required on every route in `routes/api.php`, including invitation routes. |
| HMAC | Implemented in `VerifyHmacSignature`, but not registered as an alias or assigned to these routes. Sending a signature currently does not enable HMAC verification. |
| User authorization | The current route group does not require `auth:sanctum` or a staff permission check. An API key does not establish a user's permissions. |
| Session middleware | The group includes `web` and declares a `PreventRequestForgery` exclusion. Browser/session security needs a separate review before using this as a staff-facing API. |

Creating this guide does not enable HMAC or change the middleware setup.

## 1. URL changes for existing clients

Every path below is relative to the API origin. Keep the `/api` prefix.

| Previous path | Current path |
| --- | --- |
| `/api/application` | `/api/applications` |
| `/api/application/{application}` | `/api/applications/{application}` |
| `/api/application/{application}/application-answer` | `/api/applications/{application}/application-answers` |
| `/api/application/{application}/application-answer/{applicationAnswer}` | `/api/applications/{application}/application-answers/{application_answer}` |
| `/api/form-sections/{formSection}/re-order` | `/api/form-sections/{form_section}/position` |
| `/api/form-fields/{formField}/re-order` | `/api/form-fields/{form_field}/position` |
| `/api/form-field-options/{formFieldOption}/re-order` | `/api/form-field-options/{form_field_option}/position` |
| `/api/application-invitations/{token}/submit` | `/api/application-invitations/{token}/submissions` |

The previous paths are not registered as compatibility aliases. Update frontend calls, server integrations, Postman requests, and bookmarks that use them.

Parameter labels now follow Laravel's snake_case resource defaults. This changes associative keys passed to Laravel's `route()` helper, not the UUID values sent over HTTP.

## 2. Route reference

Resource routes use `index`, `store`, `show`, `update`, and `destroy` names. Updates accept both `PUT` and `PATCH`. Nested form resources use shallow nesting: list/create routes include their parent; individual resources have a shorter URL. This follows [Laravel resource routing](https://laravel.com/framework/docs/13.x/controllers#resource-controllers).

`GET` routes also support `HEAD`. Replace model placeholders with existing UUIDs. The `{token}` placeholder is the raw invitation token.

| Method | Path | Laravel route name |
| --- | --- | --- |
| GET, HEAD | `/api/application-invitations/{token}/form` | `application-invitations.form.show` |
| POST | `/api/application-invitations/{token}/submissions` | `application-invitations.submissions.store` |
| GET, HEAD | `/api/applications` | `applications.index` |
| GET, HEAD | `/api/applications/{application}` | `applications.show` |
| GET, HEAD | `/api/applications/{application}/application-answers` | `applications.application-answers.index` |
| GET, HEAD | `/api/applications/{application}/application-answers/{application_answer}` | `applications.application-answers.show` |
| GET, HEAD | `/api/departments` | `departments.index` |
| POST | `/api/departments` | `departments.store` |
| GET, HEAD | `/api/departments/{department}` | `departments.show` |
| PUT, PATCH | `/api/departments/{department}` | `departments.update` |
| DELETE | `/api/departments/{department}` | `departments.destroy` |
| GET, HEAD | `/api/employment-types` | `employment-types.index` |
| POST | `/api/employment-types` | `employment-types.store` |
| GET, HEAD | `/api/employment-types/{employment_type}` | `employment-types.show` |
| PUT, PATCH | `/api/employment-types/{employment_type}` | `employment-types.update` |
| DELETE | `/api/employment-types/{employment_type}` | `employment-types.destroy` |
| GET, HEAD | `/api/form-field-options/{form_field_option}` | `form-field-options.show` |
| PUT, PATCH | `/api/form-field-options/{form_field_option}` | `form-field-options.update` |
| DELETE | `/api/form-field-options/{form_field_option}` | `form-field-options.destroy` |
| PATCH | `/api/form-field-options/{form_field_option}/position` | `form-field-options.position.update` |
| GET, HEAD | `/api/form-fields/{form_field}` | `form-fields.show` |
| PUT, PATCH | `/api/form-fields/{form_field}` | `form-fields.update` |
| DELETE | `/api/form-fields/{form_field}` | `form-fields.destroy` |
| GET, HEAD | `/api/form-fields/{form_field}/options` | `form-fields.options.index` |
| POST | `/api/form-fields/{form_field}/options` | `form-fields.options.store` |
| PATCH | `/api/form-fields/{form_field}/position` | `form-fields.position.update` |
| GET, HEAD | `/api/form-sections/{form_section}` | `form-sections.show` |
| PUT, PATCH | `/api/form-sections/{form_section}` | `form-sections.update` |
| DELETE | `/api/form-sections/{form_section}` | `form-sections.destroy` |
| GET, HEAD | `/api/form-sections/{form_section}/form-fields` | `form-sections.form-fields.index` |
| POST | `/api/form-sections/{form_section}/form-fields` | `form-sections.form-fields.store` |
| PATCH | `/api/form-sections/{form_section}/position` | `form-sections.position.update` |
| GET, HEAD | `/api/form-templates` | `form-templates.index` |
| POST | `/api/form-templates` | `form-templates.store` |
| GET, HEAD | `/api/form-templates/{form_template}` | `form-templates.show` |
| PUT, PATCH | `/api/form-templates/{form_template}` | `form-templates.update` |
| DELETE | `/api/form-templates/{form_template}` | `form-templates.destroy` |
| GET, HEAD | `/api/form-templates/{form_template}/form-versions` | `form-templates.form-versions.index` |
| POST | `/api/form-templates/{form_template}/form-versions` | `form-templates.form-versions.store` |
| GET, HEAD | `/api/form-versions/{form_version}` | `form-versions.show` |
| PUT, PATCH | `/api/form-versions/{form_version}` | `form-versions.update` |
| DELETE | `/api/form-versions/{form_version}` | `form-versions.destroy` |
| GET, HEAD | `/api/form-versions/{form_version}/form-sections` | `form-versions.form-sections.index` |
| POST | `/api/form-versions/{form_version}/form-sections` | `form-versions.form-sections.store` |
| GET, HEAD | `/api/job-hirings` | `job-hirings.index` |
| POST | `/api/job-hirings` | `job-hirings.store` |
| GET, HEAD | `/api/job-hirings/{job_hiring}` | `job-hirings.show` |
| PUT, PATCH | `/api/job-hirings/{job_hiring}` | `job-hirings.update` |
| DELETE | `/api/job-hirings/{job_hiring}` | `job-hirings.destroy` |
| GET, HEAD | `/api/job-hirings/{job_hiring}/form-versions` | `job-hirings.form-versions.index` |
| POST | `/api/job-hirings/{job_hiring}/form-versions` | `job-hirings.form-versions.store` |
| DELETE | `/api/job-hirings/{job_hiring}/form-versions/{form_version}` | `job-hirings.form-versions.destroy` |
| POST | `/api/job-hirings/{job_hiring}/invitations` | `job-hirings.invitations.store` |
| GET, HEAD | `/api/locations` | `locations.index` |
| POST | `/api/locations` | `locations.store` |
| GET, HEAD | `/api/locations/{location}` | `locations.show` |
| PUT, PATCH | `/api/locations/{location}` | `locations.update` |
| DELETE | `/api/locations/{location}` | `locations.destroy` |

### Generate URLs in Laravel

Use route names instead of hardcoding paths in PHP:

```php
$departmentsUrl = route('departments.index');

$answerUrl = route('applications.application-answers.show', [
    'application' => $application,
    'application_answer' => $answer,
]);
```

The nested answer route binds both models. An answer that does not belong to the application in the URL returns `404`. This parent-child check does not replace a user authorization policy.

### Position request bodies

Sections and options accept a `sort_order` value:

```json
{
  "sort_order": 2
}
```

Fields also require the destination section's UUID, even when staying in the same section:

```json
{
  "form_section_id": "0d4ee847-f9b6-4553-87bb-856539739cad",
  "sort_order": 2
}
```

These examples describe the position endpoints only. Other request bodies must follow their Form Requests in `app/Http/Requests`.

## 3. API-key configuration

The Laravel server reads these values through [config/services.php](config/services.php):

```dotenv
HEADER_API=X-API-Key
JAO_API_KEY=replace-with-a-generated-api-key
JAO_HMAC_SECRET=replace-with-a-different-generated-secret
CACHE_STORE=database
```

Generate two independent values by running this command twice. Put one output in `JAO_API_KEY` and the other in `JAO_HMAC_SECRET`. The placeholder values above are not credentials to deploy.

```bash
php -r 'echo bin2hex(random_bytes(32)), PHP_EOL;'
```

Use the generated text exactly as stored. The HMAC code uses the secret string directly; clients must not hex-decode it first.

After editing local environment values, clear cached configuration:

```bash
php artisan config:clear --no-interaction
```

If production uses cached configuration, rebuild it during deployment:

```bash
php artisan config:cache --no-interaction
```

Keep these credentials on trusted servers or in a secret manager. Never expose them through browser JavaScript, `VITE_*`, or `NEXT_PUBLIC_*` variables. Send API keys in headers and use HTTPS in production, as recommended by the [OWASP REST security guidance](https://cheatsheetseries.owasp.org/cheatsheets/REST_Security_Cheat_Sheet.html#api-keys).

A request under the current API-key-only setup looks like this. Set the shell variables privately first; the API base URL is the origin, without a trailing `/api`.

```bash
curl --fail-with-body \
  "$JAO_API_BASE_URL/api/departments" \
  -H "Accept: application/json" \
  -H "X-API-Key: $JAO_API_KEY"
```

If you change `HEADER_API`, change the API-key header in your clients too. Missing, incorrect, or blank configured keys return `401`.

## 4. Enable HMAC for trusted server requests

The following changes are setup instructions. They have not been applied by this guide.

### Register the middleware alias

In [bootstrap/app.php](bootstrap/app.php), import the HMAC middleware:

```php
use App\Http\Middleware\VerifyHmacSignature;
```

Add the alias to the existing alias array, keeping its other entries:

```php
$middleware->alias([
    'jao.key' => JaoApiKey::class,
    'jao.hmac' => VerifyHmacSignature::class,
    'application.invitation' => ValidateApplicationInvitation::class,
]);
```

### Assign HMAC to the server route group

For a group called only by trusted backend servers, use both checks:

```php
Route::middleware(['jao.key', 'jao.hmac'])->group(function () {
    // Keep the existing route definitions here.
});
```

This is the API route group in `routes/api.php`; the framework already supplies its `api` middleware.

When converting the current group into this dedicated server group, remove its `web` entry, the trailing `->withoutMiddleware(PreventRequestForgery::class)`, and the now-unused `PreventRequestForgery` import. Keep browser/session routes under appropriate CSRF protection. Sanctum's `statefulApi()` can still apply session middleware to requests from configured frontend origins; HMAC does not bypass that protection.

Configure the non-empty key and HMAC secret before enabling the group, and update the callers to sign requests before switching it on. A caller with only an API key will receive `401` once HMAC is required.

## 5. HMAC signing contract

The implementation is [VerifyHmacSignature.php](app/Http/Middleware/VerifyHmacSignature.php). It uses HMAC-SHA256.

### Required headers after HMAC is enabled

| Header | Value |
| --- | --- |
| `X-API-Key` | The configured API key; use `HEADER_API` if customized. |
| `X-Timestamp` | Unix time in seconds, as decimal digits. Do not use milliseconds. |
| `X-Nonce` | A fresh UUID for each request. |
| `X-Signature` | The 64-character lowercase hexadecimal HMAC-SHA256 digest. |
| `Accept` | `application/json`. |
| `Content-Type` | `application/json` when sending a JSON body. |

Do not send the HMAC secret in a request.

### Build the signed payload

Join these five values with a single newline character, in this exact order:

```text
UPPERCASE_METHOD
REQUEST_URI
TIMESTAMP
NONCE
RAW_BODY
```

Equivalent PHP:

```php
$payload = implode("\n", [
    $method,
    $requestUri,
    $timestamp,
    $nonce,
    $body,
]);

$signature = hash_hmac('sha256', $payload, $hmacSecret);
```

The request URI includes `/api`, the encoded path, and the query string exactly as Laravel receives them. It excludes the scheme and host. Query order and encoding matter: `?page=1&status=published` differs from `?status=published&page=1`. Account for any reverse-proxy path rewriting.

The body is the exact transmitted body, not parsed JSON. Stringify JSON once, sign that string, and send the same string. Whitespace, property order, Unicode encoding, and trailing newlines affect the signature.

For a bodyless `GET`, use an empty string for the fifth value. The payload then ends with the newline after the nonce. Do not add another newline.

### Time, replay protection, and retries

The timestamp may differ from the server's current time by at most 300 seconds in either direction. Keep client and server clocks synchronized.

After validating the signature, the middleware atomically stores the nonce in the default Laravel cache before calling the controller. It keeps the nonce through the signed timestamp plus 301 seconds. Reusing it while that cache entry exists returns `401`, even if a previous controller request failed validation.

Use a fresh UUID and recompute the signature for a retry. This prevents replay; it does not make a write request idempotent. After a lost response, a repeated write with a new nonce may perform the operation twice.

Use a persistent cache with atomic `add` support, shared by every API server, such as a correctly configured database or Redis store. Avoid `array`, `null`, local-only stores across multiple servers, or failover to a per-process cache for this boundary. Cache eviction or clearing can remove replay records before their intended expiry; isolate and size this cache accordingly.

Cache failures are not converted into successful authentication. They may surface as server errors; fix the cache instead of bypassing signature checks.

## 6. Node.js server example

This example uses Node.js 22 and its built-in [crypto API](https://nodejs.org/docs/latest-v22.x/api/crypto.html#cryptocreatehmacalgorithm-key-options). No additional package is needed.

Inject `JAO_API_BASE_URL`, `JAO_API_KEY`, and `JAO_HMAC_SECRET` into the server process environment. For example, the base URL could be `https://api.example.com`; replace it with your API origin. This code does not load an environment file automatically.

Run this in a server module, not a browser component:

```javascript
import { createHmac, randomUUID } from 'node:crypto';

const baseUrl = process.env.JAO_API_BASE_URL;
const apiKey = process.env.JAO_API_KEY;
const hmacSecret = process.env.JAO_HMAC_SECRET;

if (!baseUrl || !apiKey || !hmacSecret) {
    throw new Error('Missing JAO API configuration.');
}

const method = 'GET';
const url = new URL('/api/departments?page=1', baseUrl);
const requestUri = url.pathname + url.search;
const body = '';
const timestamp = String(Math.floor(Date.now() / 1000));
const nonce = randomUUID();

const payload = [method, requestUri, timestamp, nonce, body].join('\n');
const signature = createHmac('sha256', hmacSecret)
    .update(payload, 'utf8')
    .digest('hex');

const response = await fetch(url, {
    method,
    redirect: 'error',
    headers: {
        Accept: 'application/json',
        'X-API-Key': apiKey,
        'X-Timestamp': timestamp,
        'X-Nonce': nonce,
        'X-Signature': signature,
    },
});

if (!response.ok) {
    throw new Error(
        'JAO API request failed (' + response.status + '): ' + await response.text()
    );
}

const result = await response.json();
console.log(result);
```

For a JSON write, set the method to `POST`, `PUT`, or `PATCH` and create the body before signing:

```javascript
const body = JSON.stringify({
    name: 'Operations',
    code: 'OPS',
    is_active: true,
});
```

Pass this same `body` string in the `fetch` options and include `Content-Type: application/json`. Do not provide a body option for `GET` or `HEAD`.

### Fixed signing example

Use these public example values to compare implementations. This fixed timestamp is for local signature checks only; it will not pass a live freshness check outside its five-minute window.

| Input | Value |
| --- | --- |
| Method | `POST` |
| URI | `/api/departments` |
| Timestamp | `1793102400` |
| Nonce | `b74add62-5d68-434c-9717-924149acb32a` |
| Secret | `example-signing-secret` |
| Raw body | `{"name":"Operations","code":"OPS","is_active":true}` |

Expected lowercase signature:

```text
3f118c3daa03e2dbf640f5df505c960ea119306fee284d1693645201112c3784
```

## 7. Authentication and authorization

The shared API key identifies a trusted caller. HMAC proves possession of the signing secret and checks the signed request data. Neither check grants a staff role or establishes which user's records may be read or changed.

For endpoints used by people, use Sanctum authentication plus explicit gates or policies. Keep the integration secrets in your backend. Browser clients should authenticate through their user session or an appropriate user-token flow, with the corresponding CSRF and authorization checks. See [Laravel Sanctum](https://laravel.com/framework/docs/13.x/sanctum) and [Laravel authorization](https://laravel.com/framework/docs/13.x/authorization).

The current route file does not implement that user permission boundary. Invitation tokens also do not bypass the API-key middleware.

There is one configured API key and one HMAC secret. There is no per-client key store or overlapping rotation window. Rotate credentials by coordinating server and caller configuration changes, then refreshing cached configuration. Do not record credentials in request logs.

## 8. Troubleshooting and checks

| Result | Check |
| --- | --- |
| `401` with the current API-key setup | The header name, key value, blank server configuration, and cached configuration. |
| `401` after HMAC is enabled | The secret, lowercase digest, exact URI/query/body bytes, timestamp units, clock skew, UUID nonce, and nonce reuse. |
| Signature headers have no effect | Confirm the HMAC alias is registered and assigned to the route. It is not assigned in the current code. |
| `404` after URL migration | Use the new plural paths, `/position`, or `/submissions`. |
| `404` for a nested answer | Both records must exist, and the answer must belong to the application in the URL. |
| `405` | Match the route's HTTP method. |
| `419` | Inspect session/CSRF middleware and request origin. A valid API key or signature does not bypass CSRF. |
| `422` | Fix the request body using the endpoint's Form Request; HMAC verification does not replace field validation. |
| Cache-related `500` after HMAC is enabled | Check cache connectivity, tables or Redis configuration, and shared-store availability. |

Inspect the live route definitions:

```bash
php artisan route:list --path=api --except-vendor -vv --no-interaction
```

Run the focused route and credential tests:

```bash
php artisan test --compact tests/Feature/ApiRoutingTest.php tests/Feature/Http/Middleware/JaoApiKeyTest.php tests/Feature/Http/Middleware/VerifyHmacSignatureTest.php
```
