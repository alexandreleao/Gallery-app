<div class="image">
    <img src="{{$url}}" alt="Imagem qualquer" />
    <div class="overlay">
        <h2>{{$title}}</h2>
        <a href="{{route('delete',$id)}}" class="btn-delete">
            <img src="./assets/icons/btn_delete.png" alt="Deletar imagem" />
        </a>
    </div>
</div>