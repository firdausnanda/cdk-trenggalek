<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
  protected $fillable = [
    'post_id',
    'name',
    'email',
    'content',
    'is_approved'
  ];

  protected $table = 'comments';

  public function post()
  {
    return $this->belongsTo(Post::class);
  }
}
