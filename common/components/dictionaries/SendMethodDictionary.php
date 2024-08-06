<?php

namespace common\components\dictionaries;

class SendMethodDictionary extends BaseDictionary
{
    const PERSONALLY = 0;
    const POST = 1;
    const EMAIL = 2;
    const FAX = 3;
    const WHATSAPP = 5;
    const SBIS = 6;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::PERSONALLY => 'Лично',
            self::POST => 'Почта',
            self::EMAIL => 'E-mail',
            self::FAX => 'Факс',
            self::WHATSAPP => 'WhatsApp',
            self::SBIS => 'СБИС',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::PERSONALLY],
            $this->list[self::POST],
            $this->list[self::EMAIL],
            $this->list[self::FAX],
            $this->list[self::WHATSAPP],
            $this->list[self::SBIS],
        ];
    }
}