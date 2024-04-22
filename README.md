# PHP shortcodes handler
## Based on Wordpress sources for my personal purposes
### Example
    
    require_once __DIR__.'/vendor/autoload.php';
    
    function sampleFunc($params) {
        $atts = $params["atts"];
        $args = $params["args"];

        $name = $atts["name"];
        $age = $args["age"];
        
        return "<p>Name: $name</p><p>Age: $age</p>";
    }

    $content = '
        <p>Some other elements</p>
        [sample name="Alex"]
        <p>Some other elements</p>
    ';

    $handler = new ShortcodeHandler();
    $handler->addShortcode('sample', 'sampleFunc', ['age' => 18]);

    $content = $handler->handle($content);
    print_r($content);

Result:

    <p>Some other elements</p>
    <p>Name: Alex</p><p>Age: 18</p>
    <p>Some other elements</p>

### Links
If you're new at PHP, you can read more about callback functions here:
https://www.php.net/manual/en/language.types.callable.php