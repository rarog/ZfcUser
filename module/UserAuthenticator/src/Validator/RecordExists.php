<?php

namespace UserAuthenticator\Validator;

class RecordExists extends AbstractRecord
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
        if (! $result) {
            $valid = false;
            $this->error(self::ERROR_NO_RECORD_FOUND);
        }

        return $valid;
    }
}
