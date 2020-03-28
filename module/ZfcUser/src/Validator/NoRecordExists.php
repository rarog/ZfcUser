<?php

namespace ZfcUser\Validator;

class NoRecordExists extends AbstractRecord
{
    /**
     * {@inheritDoc}
     * @see \Laminas\Validator\ValidatorInterface::isValid()
     */
    public function isValid($value)
    {
        $valid = true;
        $this->setValue($value);

        $result = $this->query($value);
        if ($result) {
            $valid = false;
            $this->error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }
}
