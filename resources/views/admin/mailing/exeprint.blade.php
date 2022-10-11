<!DOCTYPE html>
<html>
    <body onload=" printDocument('pdfpreview');"> 
        <form action="{{  url()->previous() }}" method="GET">
                <input type="submit" value="<< Back" />
            </form>
        
        <iframe src="{{$url}}#toolbar=0&navpanes=0&scrollbar=0"  id="pdfpreview" style="width: 100%; height:97vh"></iframe>
    </body>
    <script>
        function printDocument(documentId) {
            var doc = document.getElementById(documentId);
            doc.focus();
            doc.contentWindow.print();
            //Wait until PDF is ready to print    
            
           


            
        }
        
        
    
    </script>
</html>