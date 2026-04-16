-- ============================================================
--  setup.sql — I-paste ito sa Supabase SQL Editor
--  Supabase Dashboard → SQL Editor → New Query → i-paste → Run
-- ============================================================

-- 1. GUESTS TABLE
CREATE TABLE IF NOT EXISTS public.guests (
    id            BIGSERIAL PRIMARY KEY,
    guest_number  VARCHAR(25)  NOT NULL UNIQUE,
    name          VARCHAR(100) NOT NULL,
    contact_no    VARCHAR(20)  NOT NULL,
    email         VARCHAR(100) NOT NULL,
    company_name  VARCHAR(100) NOT NULL,
    registered_at TIMESTAMPTZ  NOT NULL DEFAULT now()
);

-- 2. ENABLE ROW LEVEL SECURITY
ALTER TABLE public.guests ENABLE ROW LEVEL SECURITY;

-- 3. RLS POLICIES

-- Anyone can INSERT (guests registering at the kiosk)
CREATE POLICY "allow_public_insert"
  ON public.guests
  FOR INSERT
  TO anon, authenticated
  WITH CHECK (true);

-- Only logged-in admin can SELECT (view guest list in dashboard)
CREATE POLICY "allow_auth_select"
  ON public.guests
  FOR SELECT
  TO authenticated
  USING (true);

-- Only logged-in admin can DELETE
CREATE POLICY "allow_auth_delete"
  ON public.guests
  FOR DELETE
  TO authenticated
  USING (true);

-- 4. INDEX for fast guest_number prefix search (for numbering)
CREATE INDEX IF NOT EXISTS idx_guest_number ON public.guests (guest_number);

-- 5. INDEX for date-based stats query
CREATE INDEX IF NOT EXISTS idx_registered_at ON public.guests (registered_at);
