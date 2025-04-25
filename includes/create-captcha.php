<?php

require_once __DIR__ . '/../vendor/gregwar/captcha/src/Gregwar/Captcha/CaptchaBuilderInterface.php';
require_once __DIR__ . '/../vendor/gregwar/captcha/src/Gregwar/Captcha/PhraseBuilderInterface.php';
require_once __DIR__ . '/../vendor/gregwar/captcha/src/Gregwar/Captcha/PhraseBuilder.php';
require_once __DIR__ . '/../vendor/gregwar/captcha/src/Gregwar/Captcha/CaptchaBuilder.php';
require_once __DIR__ . '/../vendor/gregwar/captcha/src/Gregwar/Captcha/ImageFileHandler.php';

use Gregwar\Captcha\CaptchaBuilder;

function createCaptcha()
{
  $builder = new CaptchaBuilder;
  $builder->build();

  $_SESSION['captcha'] = $builder->getPhrase();

  return $builder->inline();
}


