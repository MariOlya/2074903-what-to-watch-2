<?php

namespace App\Factories\Dto;

class HtmlAcademyFilmApiDto extends Dto
{
    /**
     * To fill additional info (not included in Omdb API) you need only 'icon' (preview image),
     * 'background' (background image), 'video' (video link), 'preview' (preview video link)
     *
     * @param array $params Includes 'name', 'desc', 'director', 'actors[]', 'run_time', 'released', 'genres[]',
     * 'poster', 'icon', 'background', 'video', 'preview'
     */
    public function __construct(
        array $params,
    )
    {
        $this->setParams($params);
    }
}
