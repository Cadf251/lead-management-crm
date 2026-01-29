ALTER TABLE leads
  DROP email_hash,
  DROP celular_hash,
  CHANGE email email varchar(255) NULL,
  CHANGE celular celular varchar(15) NULL;