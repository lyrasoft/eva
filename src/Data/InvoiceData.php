<?php

declare(strict_types=1);

namespace App\Data;

use Windwalker\Data\ValueObject;

class InvoiceData extends ValueObject
{
    public string $title = '';

    public string $vat = '';

    public string $no = '';

    public string $date = '';

    public string $address = '';

    public string $mobile = '';

    public string $carrierCode = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getVat(): string
    {
        return $this->vat;
    }

    public function setVat(string $vat): static
    {
        $this->vat = $vat;

        return $this;
    }

    public function getNo(): string
    {
        return $this->no;
    }

    public function setNo(string $no): static
    {
        $this->no = $no;

        return $this;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getCarrierCode(): string
    {
        return $this->carrierCode;
    }

    public function setCarrierCode(string $carrierCode): static
    {
        $this->carrierCode = $carrierCode;

        return $this;
    }
}
