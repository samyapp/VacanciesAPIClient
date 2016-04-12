# API Client for the <redacted> Vacancy Search API #

Version 0.1 of a client to access <redacted> applicant tracking vacancy search API. 

## Example Usage ##

```php
use \SamYapp\VacanciesAPI;

$api = new SamYapp\VacanciesAPI\Request();
$api->public_key = '<your-api-key>';
$api->api_url = '<your-api-url>';
$api->job_frames = '<job-frame-1>,<job-frame-2>';
$api->company_id = '<your_company_id>';
$api->limit = 10;
$vacancies = $api->request();
```
