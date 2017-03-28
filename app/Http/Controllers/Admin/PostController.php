<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Jobs\PostForm;
use App\Post;

class PostController extends Controller
{
	/**
	 * Display a listing of the posts
	 */
    public function index()
	{	
    	return view('admin.post.index')
    				->withPosts(Post::all());
	}

	/**
	 * Show the new post form
	 */
	public function create()
	{
		$data = $this->dispatch(new PostForm());

		return view('admin.post.create', $data);
	}

	/**
	 * Store a newly created Post
	 *
	 * @param PostCreateRequest $request
	 */
	public function store(PostCreateRequest $request)
	{
		$post = Post::create($request->postFillData());
		$post->syncTags($request->get('tags', []));

		return redirect('/admin/post')
						->withSuccess('New Post Successfully Created');
	}

	/**
	 * Show the post edif form
	 *
	 * @param int $id
	 * @return Response
	 */
	public function edit($id)
	{
		$data = dispatch(new PostForm($id));

		return view('admin.post.edit', $data);
	}

	/**
	 * Update the Post 
	 *
	 * @param PostUpdateRequest $request
	 * @param int $id
	 */
	public function update(PostUpdateRequest $request, $id)
	{
		$post = Post::findOrFail($id);
		$post->fill($request->postFillData());
		$post->save();
		$post->syncTags($request->get('tags', []));

		if ($request->action === 'continue') {
			return redirect()
							->back()
							->withSuccess('Post saved');
		}

		return redirect('/admin/post')
						->withSuccess('Post saved');
	}

	/**
	 * Remove the specified resource from storage
	 *
	 * @param int $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$post = Post::findOrFail($id);
		$post->tags()->detach();
		$post->delete();

		return redirect('/admin/post')
						->withSuccess('Post deleted');
	}
}
