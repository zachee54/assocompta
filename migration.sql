-- Allow NULL for debit and credit

ALTER TABLE ecritures
  MODIFY debit DECIMAL(8,2) DEFAULT NULL,
  MODIFY credit DECIMAL(8,2) DEFAULT NULL;

UPDATE ecritures
  SET debit = NULL
  WHERE debit = 0;

UPDATE ecritures
  SET credit = NULL
  WHERE credit = 0;
