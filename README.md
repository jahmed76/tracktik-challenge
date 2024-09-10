# TrackTik Integration Project

This Symfony-based PHP project integrates with TrackTik's API to manage employee data. It includes a RESTful API to handle synchronization from two different identity providers (`provider_a` and `provider_b`) and interacts with a PostgreSQL database.

## Prerequisites

- Docker
- Docker Compose

## Project Setup

### Step 1: Setup env

We need to add these env variables in .env file before we can start the app

```
TRACKTIK_BASE_URL=https://smoke.staffr.net
TRACKTIK_OAUTH_CODE_URL=/rest/oauth2/authorize
TRACKTIK_OAUTH_TOKEN_URL=/rest/oauth2/access_token
TRACKTIK_CLIENT_ID= #Client id from credentials.json file
TRACKTIK_CLIENT_SECRET= #Client secret from credentials.json file
TRACKTIK_REFRESH_TOKEN= #Initial refresh token from credentials.json file

DATABASE_URL="postgresql://admin:root@host.docker.internal:15432/tracktik_db?serverVersion=16&charset=utf8"
```

TRACKTIK_REFRESH_TOKEN is the initial refresh token, which is used to generate subsequent access tokens and refresh tokens.

### Step 1: Start the Docker Containers

Run the following command to start the PHP application, PostgreSQL, and pgAdmin containers:

```bash
cd to docker
docker compose up
```
This should start the symfony app, postgres and pgadmin.

### Step 2: Set Up the Database
Once the containers are running, we need to set up the PostgreSQL database. Use the following commands to create and configure the database schema:

1. Create the database:

`docker exec tracktik-web php bin/console doctrine:database:create`

2. Run the migrations:

`docker exec tracktik-web php bin/console doctrine:migrations:migrate`


## API Endpoints

The application exposes the following API endpoint:
`/api/sync/{provider}`:

Method: `POST`

Parameters: `{provider}` can be either `provider_a` or `provider_b`

Description: This endpoint synchronizes employee data from the specified provider

Payload: The payload format differs between `provider_a` and `provider_b`:

Payload for provider_b:

```
{
  "first_name": "John",
  "last_name": "Doe",
  "email_address": "john.doe@example.com",
  "phone": "123-456-7890",
  "position": "Manager",
  "gender": "Male", // enum: Male, Female, Other
  "birth_date": "1990-01-01" // yyyy-mm-dd
}
```

Payload for provider_b:

```
{
  "fname": "John",
  "lname": "Doe",
  "contact": {
    "email": "john.doe@example.com",
    "address": {
      "street": "123 Main St",
      "city": "New York",
      "zip": "10001"
    }
  },
  "phone": "123-456-7890",
  "role": "Developer",
  "sex": "male", // enum: male, female, other
  "dob": {
    "year": 1990,
    "month": 5,
    "day": 15
  }
}
```

```
curl -X POST http://localhost:9080/api/sync/provider_a -H "Content-Type: application/json" -d '{"first_name": "John", "last_name": "Doe", "email": "john.doe@example.com", "gender": "Male"}'
```

## Running Tests

To run the test suite, we need to set up a separate testing database. Use the following commands to create the test database and schema:

1. Create the test database:

`docker exec tracktik-web php bin/console --env=test doctrine:database:create`

2. Set up the test schema:

`docker exec tracktik-web php bin/console --env=test doctrine:schema:create`

3. Run the test cases:

`docker exec tracktik-web php bin/phpunit`

## Docker Services:

The project uses the following Docker services:

1. tracktik-web: Symfony application (PHP).
2. postgres: PostgreSQL database.
3. pgadmin: pgAdmin to manage the database.

##  Database Access

- pgAdmin: Access pgAdmin at http://localhost:5050 with the following credentials:
```
Email: admin@admin.com
Password: root
```

PostgreSQL: The database is available at `localhost:15432` with the following credentials:
```
Username: admin
Password: root
Database Name: tracktik_db
```