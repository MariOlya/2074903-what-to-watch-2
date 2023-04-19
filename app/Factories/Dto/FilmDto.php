<?php

namespace App\Factories\Dto;

class FilmDto extends Dto
{
    /**
     * @param array $params Includes 'name', 'poster_image', 'preview_image', 'background_image', 'background_color',
     * 'video_link', 'preview_video_link', 'description', 'director', 'starring', 'genre', 'run_time', 'released',
     * 'imdb_id', 'status'
     */
    public function __construct(
        array $params,
    )
    {
        $this->setParams($params);
    }
}
