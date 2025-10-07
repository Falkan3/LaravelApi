# LaravelApi

A test project to showcase API design.

[My GitHub](https://github.com/Falkan3)

## Portfolio Project Note

This repository contains code written solely by me, Adam Kocić (Falkan3), and is intended for viewing purposes only.

Its primary purpose is to serve as a personal portfolio piece to allow potential employers or recruiters to assess my technical skills and coding abilities.

Viewing, cloning, and reviewing the code is permitted. Any other use, including commercial use, reproduction, distribution, or modification, is strictly prohibited without the express written permission of the copyright holder.

## Usage

See `commands` folder to find actions that can be performed.

### List of commands

- companies
  - create a company: `api:companies:create {name} {tax_id} {country_code} {city} {address} {post_code}`
  - update a company: `api:companies:create {id} {--name} {--tax_id} {--country_code} {--city} {--address} {--post_code}`
  - view a company: `api:companies:view {id}`
  - destroy a company: `api:companies:destroy {id}`
  - manage company-employee link: `api:company:manageEmployees {company_id} {employee_id} {action=link|unlink}`
- employees
  - create an employee: `api:employees:create {first_name} {last_name} {email} {phone_number*}`
  - update an employee: `api:employees:create {--first_name} {--last_name} {--email} {--phone_number*}`
  - view an employee: `api:employees:view {id}`
  - destroy an employee: `api:employees:destroy {id}`
