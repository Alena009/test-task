<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public $posts;
    public $payload;

    public function setUp(): void
    {
        parent::setUp();

        $this->posts = Post::factory()->count(10)->make();

        $this->payload = [
            'title' => 'title',
            'content' => 'text',
            'publish_date' => date('Y-m-d H:i:s'),
            'status' => 1
        ];
    }    
    
    /**
     * @return 200
     * List of posts is JSON format
     * 
     * [
     *  {
     *      'id' - int,              
     *      'title' - string,
     *      'content' - string,
     *      'publish_date' - datetime,
     *      'status' - bool
     *  }, ...
     * ]
     */
    public function test_user_can_get_posts_list()
    {
        $this->json('get', "api/posts")
            ->assertStatus(200)
            ->assertJsonStructure([ 
                '*' => [
                    'id',              
                    'title',
                    'content',
                    'publish_date',
                    'status'
                ]
            ]);
    }

    public function test_user_can_delete_post()
    {
        $post = Post::create(
            $this->payload
        );
        
        $this->json('delete', "api/posts/$post->id")
             ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('posts', $this->payload);       
    } 
    
    public function test_user_can_create_post()
    {
        $this->json('post', 'api/posts', $this->payload)
             ->assertStatus(Response::HTTP_CREATED)
             ->assertJsonStructure(
                 [
                    'id',
                    'title',
                    'content',
                    'publish_date',
                    'status'
                 ]
             );

        $this->assertDatabaseHas('posts', $this->payload);        
    }
}
