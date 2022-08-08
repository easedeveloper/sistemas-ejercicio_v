$("#frmAcceso").on("submit",function(e){
    e.preventDefault();

    logina = $("#logina").val();
    clavea = $("#clavea").val();

    $.post("../ajax/usuarioajax.php?op=verificar",
        {"logina":logina,"clavea":clavea}, 
        function(data)
        {
            if (data != "null")
            {
                $(location).attr("href","categoriav.php");            
            }
            else
            {
                bootbox.alert("Usuario y/o Password incorrectos");
            }
        })
})