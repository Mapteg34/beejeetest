$(function(){
    $("form.editTaskForm .preview-btn").click(function(){
        var text = $(this).closest("form").find("[name=text]").val();
        if (text.length<=0) {
            alert("Text required, please fill field");
            return false;
        }
        $.ajax({
            url:"/tasks/preview/",
            data:{text:text},
            method:"POST",
            error:function(){
                alert("Fail get preview");
                return false;
            },
            success:function(response){
                try {
                    response = $.parseJSON(response);
                } catch(e){
                    this.error();
                    return false;
                }
                if (!response.state || (response.state!="success" && response.state!="error")) {
                    this.error();
                    return false;
                }
                if (response.state=="error") {
                    alert(response.error)
                    return false;
                }
                $(".preview-container").fadeIn();
                $(".preview-container .body").html(response.html);
                return true;
            }
        });
        return false;
    })
})