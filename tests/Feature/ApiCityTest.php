<?php

// tests/Feature/ApiCityTest.php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\EquipmentLocation;

class ApiCityTest extends TestCase
{
  use RefreshDatabase;

  public function test_can_fetch_cities()
  {
    EquipmentLocation::factory()->count(5)->create();

    $response = $this->get('/api/cities');

    $response->assertStatus(200);
    $response->assertJsonStructure([
      '*' => ['id', 'name', 'created_at', 'updated_at'],
    ]);
  }
}
