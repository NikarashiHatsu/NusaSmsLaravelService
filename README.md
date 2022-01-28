# Nusa SMS Laravel Service
The service covers all the NusaSMS's API calls endpoint.
This service is still in development. Feel free to contribute!

Complete documentation:

[https://apidoc.nusasms.com/](https://apidoc.nusasms.com/)

## Usage
1. Download or clone this project.
2. Put this project to your Laravel project directory.
3. Set the `.env` as following:
   ```env
   NUSASMS_ENV=development
   # or set NUSASMS_ENV to production
   NUSASMS_URL=https://api.nusasms.com
   NUSASMS_DEV_URL=https://dev.nusasms.com
   NUSASMS_API_KEY=<yourApiKey>
   NUSASMS_DEV_KEY=DEV_TESTING_API_KEY
   ```

## What has been covered:
- [x] Get user data using API key
- [x] Get balance data
- [x] WhatsApp Send base64 Media
- [x] WhatsApp Send message
- [x] WhatsApp Message Info
- [ ] WhatsApp Delivery Status Callback
- [ ] WhatsApp Push Inbox
- [ ] SMS Send Plain
- [ ] SMS Send Group
- [ ] SMS Command
- [ ] SMS Push Inbox
- [ ] SMS Delivery Status Callback

## Usage

### Get user data using API key:
```php
<?php

use \App\Services\NusaSms;

return response()->json(NusaSms::getUser());
```

Success response example:
```json
{
  "error": false,
  "error_code": 0,
  "message": "APIKey Authentication check",
  "data": {
    "userid": "cdev_testing_user",
    "idPerson": 1000001,
    "idClient": 1000001
  }
}
```

### Get balance data:
```php
<?php

use \App\Services\NusaSms;

return response()->json(NusaSms::getBalance());
```

Success response example:
```json
{
  "error": false,
  "error_code": 0,
  "message": "Client credit data",
  "data": {
    "idClient": 1000001,
    "wa_balance": 1000,
    "wa_expired_date": "2020-01-01",
    "hlr_balance": 0,
    "hlr_expired_date": null,
    "sim_balance": 0,
    "sim_expired_date": null,
    "sms_balance": 782,
    "sms_expired_date": "2020-12-05",
    "pulsa_balance": null
  }
}
```

### WhatsApp Send Media:
There are few notes regarding the usage of this API:
1. The `$request` parameter is an instance of `\Illuminate\Http\Request`. It is made for Laravel.
2. The file passed to the `$request` is could be anything, but are limited to 512KB of size.
3. If there are multiple file passed, the API will simply won't work. So make sure it is ONE file.
4. The "file" key could be anything.

```php
<?php

use \App\Services\NusaSms;

NusaSms::setCaption('Halo');
NusaSms::setSender('6281243214321'); // This is optional, if not set, the NusaSMS's default sender will be used.
NusaSms::setMediaAsBase64($request);

return response()->json(NusaSms::waSendBase64Media('6281212341234'));
```

Success response example:
```json
{
  "error": false,
  "error_code": 0,
  "message": "WA Send Media",
  "data": {
    "sender": "6281243214321",
    "queue": null,
    "destination": "6281212341234",
    "caption": "Halo",
    "media_url": "https://dev.nusasms.com/assets/Screenshot_20220128_224647.png",
    "ref_no": "933050291643303204"
  }
}
```

Invalid WA Sender response example:
```json
{
  "error": true,
  "message": "Invalid WA Sender",
  "description": null
}
```

Invalid destination response example:
```json
{
  "detail": [
    {
      "loc": [
        "body",
        "destination"
      ],
      "msg": "Invalid destination format",
      "type": "value_error"
    },
    {
      "loc": [
        "body",
        "__root__"
      ],
      "msg": "argument of type 'NoneType' is not iterable",
      "type": "type_error"
    }
  ]
}
```

File exceeds limit of 512KB size
```json
{
  "error": true,
  "message": "File exceed limits (max 512 KB)",
  "description": null
}
```

### WhatsApp Send Media URL:
```php
<?php

use \App\Services\NusaSms;

NusaSms::setCaption('Halo');
NusaSms::setSender('6281243214321'); // This is optional, if not set, the NusaSMS's default sender will be used.
NusaSms::setUrlMedia("https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png");

return response()->json(NusaSms::waSendBase64Media('6281212341234'));
```

Success response example:
```json
{
  "error": false,
  "error_code": 0,
  "message": "WA Send Media",
  "data": {
    "sender": "6281243214321",
    "queue": null,
    "destination": "6281212341234",
    "caption": "Halo",
    "media_url": "https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png",
    "ref_no": "644724881642721371"
  }
}
```

Invalid WA Sender response example:
```json
{
  "error": true,
  "message": "Invalid WA Sender",
  "description": null
}
```

Invalid destination response example:
```json
{
  "detail": [
    {
      "loc": [
        "body",
        "destination"
      ],
      "msg": "Invalid destination format",
      "type": "value_error"
    },
    {
      "loc": [
        "body",
        "__root__"
      ],
      "msg": "argument of type 'NoneType' is not iterable",
      "type": "type_error"
    }
  ]
}
```

File exceeds limit of 512KB size
```json
{
  "error": true,
  "message": "File exceed limits (max 512 KB)",
  "description": null
}
```

### WhatsApp Send Message:
```php
<?php

use \App\Services\NusaSms;

NusaSms::setMessage("Hello, world!");
NusaSms::setSender('6281243214321'); // This is optional, if not set, the NusaSMS's default sender will be used.

return response()->json(NusaSms::waSendMessage('6281212341234'));
```

Success response example:
```json
{
  "error": false,
  "error_code": 0,
  "message": "WA send message",
  "data": {
    "sender": "6281243214321",
    "queue": null,
    "destination": "6281212341234",
    "message": "Hello, world!",
    "ref_no": "008078391643125660"
  }
}
```

### WhatsApp Message Info:
```php
<?php

use \App\Services\NusaSms;

return response()->json(NusaSms::waGetMessageInfo("78391643125660"));
```

Success response example:
```json
{
  "error": false,
  "error_code": 0,
  "message": "Message data",
  "data": {
    "destination": "6281212341234",
    "sender": "6281243214321",
    "is_group": false,
    "create_date": "2022-01-28T23:31:50",
    "sent_date": "2022-01-28T23:31:51",
    "read_date": null,
    "delivered_date": "2022-01-28T23:31:52",
    "ref_no": "008078391643125660",
    "status": "D",
    "message": "Hello, world!",
    "caption": null,
    "media_url": null
  }
}
```