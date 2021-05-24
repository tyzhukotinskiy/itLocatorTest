<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Landing extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_header' => $this->first_header,
            'second_header' => $this->second_header,
            'content' => $this->content,
            'image' => $this->image,
            'template' => $this->template,
            'font_color' => $this->font_color,
            'domen' => $this->domen,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }

}
