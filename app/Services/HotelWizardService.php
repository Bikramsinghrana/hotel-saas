<?php

namespace App\Services;

use App\Repositories\HotelRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class HotelWizardService
{
    protected $repository;

    public function __construct(HotelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function processStep1(array $data, $hotelId = null)
    {
        return DB::transaction(function () use ($data, $hotelId) {
            $data['tenant_id'] = tenant()->id;
            if ($hotelId) {
                $hotel = $this->repository->find($hotelId);
                return $this->repository->updateBaseHotel($hotel, $data);
            }
            return $this->repository->createBaseHotel($data);
        });
    }

    public function processStep2($hotelId, array $addressData, array $nearbyPlaces)
    {
        return DB::transaction(function () use ($hotelId, $addressData, $nearbyPlaces) {
            $hotel = $this->repository->find($hotelId);
            
            return $this->repository->updateBaseHotel($hotel, [
                'address' => $addressData,
                'nearby' => array_filter($nearbyPlaces)
            ]);
        });
    }

    public function processStep3($hotelId, array $pricingData, array $facilities)
    {
        return DB::transaction(function () use ($hotelId, $pricingData, $facilities) {
            $hotel = $this->repository->find($hotelId);
            
            return $this->repository->updateBaseHotel($hotel, [
                'base_price' => $pricingData['price_per_night'] ?? 0,
                'discount' => $pricingData['discount_percentage'] ?? 0,
                'tax' => $pricingData['tax_percentage'] ?? 0,
                'facilities' => array_filter($facilities)
            ]);
        });
    }

    public function processStep4($hotelId, $file, $type)
    {
        return DB::transaction(function () use ($hotelId, $file, $type) {
            $hotel = $this->repository->find($hotelId);
            
            $path = uploadImage($file, 'hotel/' . $hotelId);

            return $this->repository->addMedia($hotel, [
                'type' => $type,
                'path' => $path,
                'disk' => 'public',
                'mime' => $file->getMimeType(),
            ]);
        });
    }

    public function processStep5($hotelId, array $policyData)
    {
        return DB::transaction(function () use ($hotelId, $policyData) {
            $hotel = $this->repository->find($hotelId);
            return $this->repository->updateBaseHotel($hotel, [
                'policies' => $policyData
            ]);
        });
    }

    public function addRoom($hotelId, array $data)
    {
        return DB::transaction(function () use ($hotelId, $data) {
            $hotel = $this->repository->find($hotelId);
            return $this->repository->createRoom($hotel, $data);
        });
    }
}
