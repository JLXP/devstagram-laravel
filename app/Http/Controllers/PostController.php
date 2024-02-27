<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    public function __construct()
    {
        //un middleware se ejecutará antes del index
        $this->middleware('auth')->except(['show', 'index']);
    }

    public function index(User $user)
    {
        //Igual se puede cambiar de paginate a simplepaginate, dependiendo de gustos
        $posts = Post::where('user_id', $user->id)->latest()->paginate(5);

        return view('dashboard', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

    //Diferencias entre create y store, create nos muestra la vista y store es para guardar la data
    public function create()
    {
        //Factory permite hacer testing a la bd
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required|max:25',
            'descripcion' => 'required',
            'imagen' => 'required'
        ]);

        /*Post::create([
            
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'imagen' => $request->imagen,
            'user_id' => auth()->user()->id
        ]);*/

        //otra forma de crear registros
        /*$post = new Post;
        $post->titulo = $request->titulo;
        $post->descripcion = $request->descripcion;
        $post->imagen = $request->imagen;
        $post->user_id = auth()->user()->id;
        $post->save();*/

        //En este create se usan las relaciones
        //Esta forma es un poco mejor ya que se ve bastante la programacion orientada a objetos
        $request->user()->posts()->create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'imagen' => $request->imagen,
            'user_id' => auth()->user()->id
        ]);

        return redirect()->route('posts.index', auth()->user()->username);
    }

    public function show(User $user, Post $post)
    {
        return view('posts.show', [
            'post' => $post,
            'user' => $user
        ]);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        //Eliminar la imagen
        $imagen_path = public_path('uploads/' . $post->imagen);

        if (File::exists($imagen_path)) {
            unlink($imagen_path);
        }

        return redirect()->route('posts.index', auth()->user()->username);
    }
}
