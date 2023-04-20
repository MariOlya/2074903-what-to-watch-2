<?php

namespace App\Factories;

use App\Factories\Interfaces\LinkFactoryInterface;
use App\Models\Link;
use App\Models\LinkType;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LinkFactory implements LinkFactoryInterface
{
    public function __construct(readonly Link $link)
    {
    }

    /**
     * @param string $link
     * @param string $type
     * @return int
     * @throws InternalErrorException
     */
    public function createNewLink(string $link, string $type): int
    {
        $linkType = LinkType::whereType($type)->first();

        if (!$linkType) {
            throw new NotFoundHttpException(
                message: 'This file type is not found',
                code: 404
            );
        }

        $this->link->link = $link;
        $this->link->link_type_id = LinkType::whereType($type)->value('id');

        if (!$this->link->save()) {
            throw new InternalErrorException('The error on the server, please, try again', 500);
        }

        return $this->link->id;
    }
}
