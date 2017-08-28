<?php

namespace queasy;

class Controller
{

    const VIEW_PATH_TEMPLATE = 'views' . DIRECTORY_SEPARATOR . '%s.php';

    private $request;

    /**
     * Constructor
     *
     * @param \queasy\HttpRequest $request Request object
     *
     * @return string Response
     */
    public function __construct(HttpRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Getter magic method, used to get request object
     *
     * @param string $property Property name
     *
     * @return \queasy\HttpRequest Request object
     */
    public function __get($property)
    {
        if ('request' === $property) {
            return $this->request;
        } else {
            throw new SystemException(sprintf('Property "%s" missing in class "%s".', $property, get_class($this)));
        }
    }

    /**
     * Generates and returns view
     *
     * @param string $name View template path, folders are separated by a dot sign
     * @param array $vars Variables to be passed into a view, defaults to an empty array
     *
     * @return string Generated view code
     */
    protected function view($name, array $vars = array())
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $name);

        $view = @file_get_contents(sprintf(self::VIEW_PATH_TEMPLATE, $path));
        if (false === $view) {
            throw new SystemException(sprintf('Can\'t locate view "%s".', $name));
        }

        return $this->generateView($view, $vars);
    }

    /**
     * Generates view from a template
     *
     * @param string $__queasyViewTemplate View template code
     * @param array $__queasyViewTemplateVars Variables to be passed into a view, defaults to an empty array
     *
     * @return string Generated view code
     */
    private function generateView($__queasyViewTemplate, array $__queasyViewTemplateVars = array())
    {
        extract($__queasyViewTemplateVars);

        try {
            $output = eval(' ?>' . $__queasyViewTemplate . '<?php ');
            if (false === $output) {
                // TODO: Handle parse error (PHP5)
            }

            return $output;
        } catch (Throwable $e) {
            // TODO: Handle parse error if $e is an instance of ParseError class (PHP7)
        }
    }

    /**
     * Includes another view into current
     *
     * @param string $include Include view template name, folders are separated by a dot sign
     * @param array $__queasyViewTemplateVars Variables to be passed into a view, defaults to an empty array
     *
     * @return string Generated view code
     */
    private function load($__queasyIncludeView, array $__queasyViewTemplateVars = array())
    {
        // TODO: Remove duplicate code (the same as in view() method)
        @include(sprintf(self::VIEW_PATH_TEMPLATE, str_replace('.', DIRECTORY_SEPARATOR, $__queasyIncludeView)));
    }

}

