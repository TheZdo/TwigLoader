# TwigLoader
Twig loading class
### How to use it
To load a template withing the construtor
    <?php
    $twig = new TwigLoader('home', array('nameParam' => 'contentParam', 'nameParam2' => 'contentParam2'), 'loadFinal');

To load a template and assign stuff to it using the methods
```
    <?php
    $twig = new TwigLoader('home');
    $twig -> _setTitle('HOME') -> _setRenders(array('user' => $userArray, 'pageContent' => $content)) -> loadFinal();
```

Another way to do it without passing anything in the contructor's parameters
```
    $twig = new TwigLoader;
    $twig -> _setTemplateName('home') -> _setTitle('HOME') -> _setRenders(array('user' => $userArray, 'pageContent' => $content)) -> loadFinal();
```

Or without using chaining
```
    <?php
    $twig = new TwigLoader;
    $twig -> _setTemplateName('home');
    $twig -> _setTitle('HOME');
    $twig -> _setRenders(array('user' => $userArray, 'pageContent' => $content));
    $twig -> loadFinal();
```
