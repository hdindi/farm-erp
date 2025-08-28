<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create v_farm_kpis view
        DB::statement("
            CREATE OR REPLACE VIEW v_farm_kpis AS
            SELECT 
                COUNT(DISTINCT sr.id) as total_sales_transactions,
                COALESCE(SUM(sr.total_amount), 0) as total_revenue_generated,
                COALESCE(SUM(sr.quantity), 0) as total_units_sold,
                COALESCE(SUM(dr.alive_count), 0) as current_alive_birds,
                COALESCE(SUM(dr.dead_count + dr.culls_count), 0) as total_deaths_culls_recorded
            FROM sales_records sr
            CROSS JOIN (
                SELECT 
                    COALESCE(SUM(alive_count), 0) as alive_count,
                    COALESCE(SUM(dead_count), 0) as dead_count,
                    COALESCE(SUM(culls_count), 0) as culls_count
                FROM daily_records 
                WHERE record_date = (SELECT MAX(record_date) FROM daily_records)
            ) dr
        ");

        // Create v_daily_egg_summary view
        DB::statement("
            CREATE OR REPLACE VIEW v_daily_egg_summary AS
            SELECT 
                dr.record_date,
                COALESCE(SUM(ep.total_eggs), 0) as total_eggs_collected,
                COALESCE(SUM(ep.good_eggs), 0) as good_eggs,
                COALESCE(SUM(ep.cracked_eggs + ep.damaged_eggs), 0) as bad_eggs
            FROM daily_records dr
            LEFT JOIN egg_production ep ON dr.id = ep.daily_record_id
            GROUP BY dr.record_date
            ORDER BY dr.record_date DESC
        ");

        // Create v_sales_by_salesperson view
        DB::statement("
            CREATE OR REPLACE VIEW v_sales_by_salesperson AS
            SELECT 
                u.name as salesperson_name,
                COUNT(sr.id) as number_of_sales,
                COALESCE(SUM(sr.total_amount), 0) as total_sales_amount,
                COALESCE(SUM(sr.amount_paid), 0) as total_amount_paid
            FROM users u
            LEFT JOIN sales_records sr ON u.id = sr.sales_person_id
            GROUP BY u.id, u.name
            ORDER BY total_sales_amount DESC
        ");

        // Create vw_batch_summary view
        DB::statement("
            CREATE OR REPLACE VIEW vw_batch_summary AS
            SELECT 
                b.batch_code,
                b.status,
                b.date_received,
                b.hatch_date,
                b.expected_end_date,
                b.initial_population,
                COALESCE(latest_dr.alive_count, b.initial_population) as current_alive_count,
                COALESCE(total_deaths.total_dead, 0) as total_deaths,
                COALESCE(total_culls.total_culls, 0) as total_culls,
                CASE 
                    WHEN b.initial_population > 0 
                    THEN ROUND(((COALESCE(total_deaths.total_dead, 0) + COALESCE(total_culls.total_culls, 0)) * 100.0 / b.initial_population), 2)
                    ELSE 0 
                END as reduction_rate_percent,
                br.name as breed_name,
                s.name as current_stage
            FROM batches b
            LEFT JOIN breeds br ON b.breed_id = br.id
            LEFT JOIN (
                SELECT 
                    batch_id, 
                    alive_count,
                    stage_id
                FROM daily_records dr1
                WHERE dr1.record_date = (
                    SELECT MAX(dr2.record_date) 
                    FROM daily_records dr2 
                    WHERE dr2.batch_id = dr1.batch_id
                )
            ) latest_dr ON b.id = latest_dr.batch_id
            LEFT JOIN stages s ON latest_dr.stage_id = s.id
            LEFT JOIN (
                SELECT 
                    batch_id, 
                    SUM(dead_count) as total_dead
                FROM daily_records 
                GROUP BY batch_id
            ) total_deaths ON b.id = total_deaths.batch_id
            LEFT JOIN (
                SELECT 
                    batch_id, 
                    SUM(culls_count) as total_culls
                FROM daily_records 
                GROUP BY batch_id
            ) total_culls ON b.id = total_culls.batch_id
        ");

        // Create vw_batch_daily_performance view
        DB::statement("
            CREATE OR REPLACE VIEW vw_batch_daily_performance AS
            SELECT 
                b.batch_code,
                dr.record_date,
                s.name as stage_name,
                dr.day_in_stage,
                dr.alive_count,
                dr.dead_count,
                dr.culls_count,
                dr.average_weight_grams,
                CASE 
                    WHEN dr.alive_count > 0 
                    THEN ROUND((dr.dead_count * 100.0 / (dr.alive_count + dr.dead_count)), 2)
                    ELSE 0 
                END as daily_mortality_rate_percent
            FROM daily_records dr
            JOIN batches b ON dr.batch_id = b.id
            LEFT JOIN stages s ON dr.stage_id = s.id
            ORDER BY b.batch_code, dr.record_date
        ");

        // Create vw_batch_feed_consumption view
        DB::statement("
            CREATE OR REPLACE VIEW vw_batch_feed_consumption AS
            SELECT 
                b.batch_code,
                dr.record_date,
                ft.name as feed_type,
                fr.quantity_kg,
                fr.cost_per_kg,
                fr.feeding_time,
                ROUND((fr.quantity_kg * fr.cost_per_kg), 4) as total_feed_cost
            FROM feed_records fr
            JOIN daily_records dr ON fr.daily_record_id = dr.id
            JOIN batches b ON dr.batch_id = b.id
            JOIN feed_types ft ON fr.feed_type_id = ft.id
            ORDER BY b.batch_code, dr.record_date, fr.feeding_time
        ");

        // Create vw_batch_vaccination_details view
        DB::statement("
            CREATE OR REPLACE VIEW vw_batch_vaccination_details AS
            SELECT 
                b.batch_code,
                dr.record_date,
                v.name as vaccine_name,
                vl.birds_vaccinated,
                vl.next_due_date,
                vl.notes
            FROM vaccination_logs vl
            JOIN daily_records dr ON vl.daily_record_id = dr.id
            JOIN batches b ON dr.batch_id = b.id
            JOIN vaccines v ON vl.vaccine_id = v.id
            ORDER BY b.batch_code, dr.record_date DESC
        ");

        // Create vw_batch_disease_management view
        DB::statement("
            CREATE OR REPLACE VIEW vw_batch_disease_management AS
            SELECT 
                b.batch_code,
                dm.observation_date,
                d.name as disease_name,
                dm.affected_count,
                dm.notes,
                dm.treatment_start_date,
                dm.treatment_end_date,
                dr.name as drug_name
            FROM disease_management dm
            JOIN batches b ON dm.batch_id = b.id
            LEFT JOIN diseases d ON dm.disease_id = d.id
            LEFT JOIN drugs dr ON dm.drug_id = dr.id
            ORDER BY b.batch_code, dm.observation_date DESC
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_batch_disease_management');
        DB::statement('DROP VIEW IF EXISTS vw_batch_vaccination_details');
        DB::statement('DROP VIEW IF EXISTS vw_batch_feed_consumption');
        DB::statement('DROP VIEW IF EXISTS vw_batch_daily_performance');
        DB::statement('DROP VIEW IF EXISTS vw_batch_summary');
        DB::statement('DROP VIEW IF EXISTS v_sales_by_salesperson');
        DB::statement('DROP VIEW IF EXISTS v_daily_egg_summary');
        DB::statement('DROP VIEW IF EXISTS v_farm_kpis');
    }
};
