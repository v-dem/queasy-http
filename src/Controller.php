<?php

namespace queasy;

class Controller
{

    const VIEW_PATH_TEMPLATE = 'views' . DIRECTORY_SEPARATOR . '%s.php';

    private $request;

    public function __construct(HttpRequest $request)
    {
        $this->request = $request;
    }

    protected function request()
    {
        return $this->request;
    }

    protected function view($name, array $vars = array())
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $name);

        $view = @file_get_contents(sprintf(self::VIEW_PATH_TEMPLATE, $path));

        if (false === $view) {
            throw new SystemException(sprintf('Can\'t locate view "%s".', $name));
        }

        return $this->generateView($view, $vars);
    }

    private function generateView($__queasyViewTemplate, array $__queasyViewTemplateVars = array())
    {
        extract($__queasyViewTemplateVars);

        return eval(' ?>' . $__queasyViewTemplate . '<?php ');
    }

}

