-- SPMC-1189 db changes

ALTER TABLE `hisdb`.`seg_cert_med`   
  ADD COLUMN `remarks_recom` TEXT NULL AFTER `procedure_verbatim`;