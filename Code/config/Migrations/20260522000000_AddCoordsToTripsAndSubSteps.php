<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddCoordsToTripsAndSubSteps extends BaseMigration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $this->table('trips')
            ->addColumn('departure_latitude', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 10,
                'scale' => 7,
                'signed' => true,
            ])
            ->addColumn('departure_longitude', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 10,
                'scale' => 7,
                'signed' => true,
            ])
            ->addColumn('arrival_latitude', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 10,
                'scale' => 7,
                'signed' => true,
            ])
            ->addColumn('arrival_longitude', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 10,
                'scale' => 7,
                'signed' => true,
            ])
            ->update();

        $this->table('sub_steps')
            ->addColumn('latitude', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 10,
                'scale' => 7,
                'signed' => true,
            ])
            ->addColumn('longitude', 'decimal', [
                'default' => null,
                'null' => true,
                'precision' => 10,
                'scale' => 7,
                'signed' => true,
            ])
            ->update();
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->table('trips')
            ->removeColumn('departure_latitude')
            ->removeColumn('departure_longitude')
            ->removeColumn('arrival_latitude')
            ->removeColumn('arrival_longitude')
            ->update();

        $this->table('sub_steps')
            ->removeColumn('latitude')
            ->removeColumn('longitude')
            ->update();
    }
}
