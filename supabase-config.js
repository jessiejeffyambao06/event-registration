// ============================================================
//  supabase-config.js — I-update lang dito ang iyong keys
//  Makukuha sa: Supabase Dashboard → Project Settings → API
// ============================================================

const SUPABASE_URL  = 'https://riapqypugimzgrkywkxn.supabase.co'; // ← palitan
const SUPABASE_ANON = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InJpYXBxeXB1Z2ltemdya3l3a3huIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzYzMDQzMDgsImV4cCI6MjA5MTg4MDMwOH0.eEpvpAY-tNPZUhfdAzHTtV4OV2lRu9kvZ0VRVY2qBfw';                // ← palitan

// Printer — kailangan pa rin ng lokal na XAMPP para sa ESC/POS printing
// Kapag nasa kiosk na ang browser at nakabukas ang XAMPP, tatawag ito sa localhost
const PRINT_ENDPOINT = 'http://192.168.254.126/anniversary/print_receipt.php';
