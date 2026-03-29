<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model // Burada 'about' yazıyorsa 'About' yap.
{
    use HasFactory;
    
    // Eğer tablo adın veritabanında 'abouts' değil de farklıysa buraya ekle:
    // protected $table = 'about'; 

    protected $fillable = [
        'title', 'description', 'second_paragraph', 'last_paragraph', 
        'image', 'phone', 'email', 'instagram_url', 'twitter_url', 
        'linkedin_url', 'github_url'
    ];
}