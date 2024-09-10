<?php

namespace App\DTO;

use DateTime;

abstract class Employee
{
    const FIRST_NAME_REQUIRED = "First name is required";
    const LAST_NAME_REQUIRED = "Last name is required";
    const GENDER_MISMATCH = "Gender format is incorrect";
    const BIRTHDAY_MISMATCH = "Birthday format is incorrect";

    public function getRequiredFields(): array
    {
        return [
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
        ];
    }

    abstract public function getProvider();
    abstract public function getFirstName();
    abstract public function getLastName();

    public function getEmail(): ?string
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return null;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getJobTitle(): ?string
    {
        return null;
    }

    public function getGender(): ?Gender
    {
        return null;
    }

    public function getBirthday(): ?string
    {
        return null;
    }

    public function prepare(): array
    {
        $gender = $this->getGender() ? $this->getGender()->value : null;
        $payload = array_merge($this->getRequiredFields(), [
            'email' => $this->getEmail(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'jobTitle' => $this->getJobTitle(),
            'gender' => $gender,
            'birthday' => $this->getBirthday(),
        ]);

        return array_filter($payload);
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function validate(): string
    {
        if (empty($this->getFirstName())) {
            return self::FIRST_NAME_REQUIRED;
        }

        if (empty($this->getLastName())) {
            return self::LAST_NAME_REQUIRED;
        }

        if (Gender::isValid($this->getGender()))  {
            return self::GENDER_MISMATCH;
        }

        $d =$this->getBirthday();
        if ($d != '' && !$this->validateDate($d)) {
            return self::BIRTHDAY_MISMATCH;
        }

        return "";
    }
}
