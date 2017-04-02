<?php

namespace queasy\validation;

use queasy\SystemException;
use queasy\ApplicationException;
use queasy\ClassNotFoundException;
use queasy\i18n\LangTrait;

class Validator
{

    use LangTrait;

    const RULE_CLASS_TEMPLATE = 'queasy\validation\%sRule';
    const MESSAGE_CODE_TEMPLATE = '%s.%s.%s';

    public static function validate(array $rules = array(), array $data = array(), $prefix = 'queasy')
    {
        $messagesMap = array();
        foreach ($rules as $fieldName => $fieldRules) {
            foreach ($fieldRules as $ruleName => $ruleParameter) {
                $ruleClassName = sprintf(self::RULE_CLASS_TEMPLATE, ucfirst($ruleName));

                try {
                    $rule = new $ruleClassName($ruleParameter);
                } catch (ClassNotFoundException $e) {
                    throw new SystemException(sprintf('Unknown rule "%s" - can\'t locate class "%s".', $ruleName, $ruleClassName));
                }

                if (!$rule->validate(isset($data[$fieldName])? $data[$fieldName]: null)) {
                    $vars = array(
                        'field' => $fieldName,
                        'parameter' => $ruleParameter
                    );

                    $message = sprintf(self::MESSAGE_CODE_TEMPLATE, $prefix, $fieldName, $ruleName);
                    $transMessage = self::trans($message, $vars);
                    if ($message === $transMessage) {
                        $message = sprintf(self::MESSAGE_CODE_TEMPLATE, __CLASS__, 'default', $ruleName);
                    }

                    $transMessage = self::trans($message, $vars);

                    if (!isset($messagesMap[$fieldName])) {
                        $messagesMap[$fieldName] = array();
                    }

                    $messagesMap[$fieldName][] = $transMessage;
                }
            }
        }

        if (count($messagesMap)) {
            throw new ValidationException($messagesMap);
        }
    }

}

