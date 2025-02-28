<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComprobanteFactory extends Factory
{
    /**
     * El nombre del modelo relacionado a esta factory.
     *
     * @var string
     */
    protected $model = Voucher::class;

    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'issuer_name' => $this->faker->company,
            'issuer_document_type' => $this->faker->randomElement(['DNI', 'RUC', 'PAS']), // Solo ejemplos
            'issuer_document_number' => $this->faker->randomNumber(8), // Ejemplo para un DNI
            'receiver_name' => $this->faker->name,
            'receiver_document_type' => $this->faker->randomElement(['DNI', 'RUC', 'PAS']),
            'receiver_document_number' => $this->faker->randomNumber(8),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000), // Genera un número flotante con 2 decimales entre 10 y 1000
            'xml_content' => $this->faker->text,
            'series' => $this->faker->randomElement(['F001', 'B001', 'T001']), // Ejemplo de series de comprobantes
            'number' => $this->faker->randomNumber(8), // Ejemplo de número de comprobante
            'voucher_type' => $this->faker->randomElement(['01', '03', '07']), // Ejemplo de tipos de comprobantes
            'currency' => $this->faker->randomElement(['PEN', 'USD']), // Ejemplo de monedas
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
