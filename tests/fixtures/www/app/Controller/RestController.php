<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RestController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function action(Request $request)
    {
        $files = $request->files->all();
        $displayFiles = [];
        /** @var UploadedFile $file */
        foreach ($files as $key => $file) {
            $displayFiles[$key] = [
                'name' => $file->getClientOriginalName(),
                'error' => $file->getError(),
                'size' => $file->getSize(),
            ];
            $file->move('/tmp/moved');
        }

        $view = 'templates/rest.php';
        $parameters = [
            'server' => $request->server->all(),
            'method' => $request->getMethod(),
            'request' => $request->request->all(),
            'files' => $displayFiles,
            'body' => $request->getContent(),
        ];
        $content = $this->container->get('templating')->render($view, $parameters);
        return new Response($content);
    }
}
