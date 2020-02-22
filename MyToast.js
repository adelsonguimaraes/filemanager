const MyToast = {
    me: null,
    timeout: null,
    create: function () {
        
        document.addEventListener("DOMContentLoaded", function () {

            var css = `.mytoast {
                height: 40px;
                width: 100%;
                bottom: 15px;
                left: 0;
                text-align: center;
                background: transparent;
                position: fixed;
                transition: all 330ms ease-in;
                -webkit-transform: translateY(160%); /* esconde menu a esquerda */
                transform: translateY(160%); /* esconde menu a esquerda */
            }
            .mytoast-show {
                -webkit-transform: none; /* esconde menu a esquerda */
                transform: none; /* esconde menu a esquerda */
                transition: all 130ms ease-in;
                z-index: 1000;
            }
            .mytoast a {
                background: #000000c9;
                padding: 10px 25px;
                border-radius: '50px';
                color: #fff;
            }`;

            var style = document.createElement('style');
            style.innerHTML = css;
            // adicionando CSS ao HTML
            document.head.appendChild(style);

            var body = document.body;
            var div = document.createElement('div');
            div.id = 'mytoast';
            div.classList.add('mytoast');
            var a = document.createElement('a');
            a.innerHTML = 'Seu texto de Toast aqui';
            
            div.appendChild(a);
            body.appendChild(div);
            MyToast.me = div;

        });
    },
    show: function (text, second) {
        text = (text !== undefined) ? text : 'Seu Texto aqui';
        MyToast.setText( text );
        MyToast.me.classList.add('mytoast-show');
        clearTimeout(MyToast.timeout);
        MyToast.timeout = setTimeout(() => {
            MyToast.close();    
        }, second*1000 || 2000);
    },
    close: function () {
        MyToast.me.classList.remove('mytoast-show');
    },
    setText: function (text) {
        MyToast.me.querySelector('a').innerHTML = text;
    }
}

MyToast.create();