@extends ('layout')

@section('content')
<form method="POST" enctype="multipart/form-data">
    @csrf
    <fieldset class="upload_dropZone text-center mb-3 p-4">
        <div class="d-flex">
              <p class="small flex-fill m-0 text-start">Select csv <i>or</i> drag & drop csv</p>
              <div>
                  <input id="upload_csv_file" data-post-name="csv_file" data-post-url="{{ route('product.index.upload.post') }}" class="position-absolute invisible" type="file" multiple accept=".csv" name="csv_file" />

                  <label class="btn btn-upload mb-3" for="upload_csv_file">Choose file(s)</label>

                  <div class="upload_gallery d-flex flex-wrap justify-content-center gap-3 mb-0"></div>
              </div>
        </div>
    </fieldset>
</form>
<div align="center" class="upload_dropZone_loading alert alert-warning">
    LOADING... Wait a moment...
</div>
<br/>
<h3 class="text-danger">Use the same "File Name" to update the list!</h3>
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ session('error') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<table id="myTable">
    <thead>
        <tr>
            <th>Time</th>
            <th>File Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if(!empty($results))
        @foreach($results as $result)
        <tr>
            <td>
                {{ date('Y-m-d h:i A', strtotime($result->updated_at)) }}<br/>
                {{ \App\Models\ProductFile::timeElapsedString($result->updated_at) }}
            </td>
            <td>
                {{ $result->filename }}
            </td>
            <td>
                {{ $result->status }}
            </td>
            <td>
                <a href="{{ route('product.detail', ['id' => $result->id]) }}" class="btn btn-primary">View</a>
                @if($result->status != 'Processing')
                <a href="{{ route('product.delete', ['id' => $result->id]) }}" class="btn btn-danger">Delete</a>
                @endif
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>

@if(!empty($results))

@endif

@endsection


@section('scripts')
<script type="text/javascript">
/* Bootstrap 5 JS included */

console.clear();
('use strict');


// Drag and drop - single or multiple image files
// https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
// https://codepen.io/joezimjs/pen/yPWQbd?editors=1000
(function () {

  'use strict';
  
  // Four objects of interest: drop zones, input elements, gallery elements, and the files.
  // dataRefs = {files: [image files], input: element ref, gallery: element ref}

  const preventDefaults = event => {
    event.preventDefault();
    event.stopPropagation();
  };

  const highlight = event =>
    event.target.classList.add('highlight');
  
  const unhighlight = event =>
    event.target.classList.remove('highlight');

  const getInputAndGalleryRefs = element => {
    const zone = element.closest('.upload_dropZone') || false;
    const gallery = zone.querySelector('.upload_gallery') || false;
    const input = zone.querySelector('input[type="file"]') || false;
    return {input: input, gallery: gallery};
  }

  const handleDrop = event => {
    const dataRefs = getInputAndGalleryRefs(event.target);
    dataRefs.files = event.dataTransfer.files;
    handleFiles(dataRefs);
  }


  const eventHandlers = zone => {

    const dataRefs = getInputAndGalleryRefs(zone);
    if (!dataRefs.input) return;

    // Prevent default drag behaviors
    ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
      zone.addEventListener(event, preventDefaults, false);
      document.body.addEventListener(event, preventDefaults, false);
    });

    // Highlighting drop area when item is dragged over it
    ;['dragenter', 'dragover'].forEach(event => {
      zone.addEventListener(event, highlight, false);
    });
    ;['dragleave', 'drop'].forEach(event => {
      zone.addEventListener(event, unhighlight, false);
    });

    // Handle dropped files
    zone.addEventListener('drop', handleDrop, false);

    // Handle browse selected files
    dataRefs.input.addEventListener('change', event => {
      dataRefs.files = event.target.files;
      handleFiles(dataRefs);
    }, false);

  }


  // Initialise ALL dropzones
  const dropZones = document.querySelectorAll('.upload_dropZone');
  for (const zone of dropZones) {
    eventHandlers(zone);
  }


  // No 'image/gif' or PDF or webp allowed here, but it's up to your use case.
  // Double checks the input "accept" attribute
  const isCsvFile = file => 
    ['application/vnd.ms-excel', 'text/csv'].includes(file.type);


  function previewFiles(dataRefs) {
      return ;
      
    if (!dataRefs.gallery) return;
    for (const file of dataRefs.files) {
      let reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onloadend = function() {
        let img = document.createElement('img');
        img.className = 'upload_img mt-2';
        img.setAttribute('alt', file.name);
        img.src = reader.result;
        dataRefs.gallery.appendChild(img);
      }
    }
  }

  const imageUpload = dataRefs => {

    // Multiple source routes, so double check validity
    if (!dataRefs.files || !dataRefs.input) return;

    const url = dataRefs.input.getAttribute('data-post-url');
    if (!url) return;

    const name = dataRefs.input.getAttribute('data-post-name');
    if (!name) return;
    
    jQuery('form').trigger('submit');
    
//    var token = jQuery('meta[name="csrf-token"]').attr('content');
//    const formData = new FormData();
//    formData.append(name, dataRefs.files);
//    formData.append('_token', token);
//
//    fetch(url, {
//      method: 'POST',
//      body: formData
//    })
//    .then(response => response.json())
//    .then(data => {
//      console.log('posted: ', data);
//      if (data.success === true) {
//        previewFiles(dataRefs);
//      } else {
//        console.log('URL: ', url, '  name: ', name)
//      }
//    })
//    .catch(error => {
//      console.error('errored: ', error);
//    });
  }


  // Handle both selected and dropped files
  const handleFiles = dataRefs => {

    let files = [...dataRefs.files];

    // Remove unaccepted file types
    files = files.filter(item => {
      if (!isCsvFile(item)) {
        console.log('Not csv, ', item.type);
        alert('Upload CSV file only');
      }
      return isCsvFile(item) ? item : null;
    });

    if (!files.length) return;
    dataRefs.files = files;

    previewFiles(dataRefs);
    imageUpload(dataRefs);
  }

    jQuery('form').submit(function(){
        const form = document.querySelector('form');
        const data = new FormData(form);
        jQuery('.upload_dropZone').hide();
        jQuery('.upload_dropZone_loading').show();
        fetch('{{ route('product.index.upload.post') }}', {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
          console.log('posted: ', data);
          if (data.status === 1) {
//            previewFiles(dataRefs);
                window.location.reload();
          } else {
              alert('Something wrong');
            console.log('URL: ', url, '  name: ', name)
          }
            jQuery('.upload_dropZone').show();
            jQuery('.upload_dropZone_loading').hide();
        })
        .catch(error => {
          console.error('errored: ', error);
        });
        return false;
    });
    
    let table = new DataTable('#myTable');
})();
</script>
@endsection



@section('styles')
<style>
    .upload_dropZone_loading{
        display:none;
    }
    
:root {
  --colorPrimaryNormal: #00b3bb;
  --colorPrimaryDark: #00979f;
  --colorPrimaryGlare: #00cdd7;
  --colorPrimaryHalf: #80d9dd;
  --colorPrimaryQuarter: #bfecee;
  --colorPrimaryEighth: #dff5f7;
  --colorPrimaryPale: #f3f5f7;
  --colorPrimarySeparator: #f3f5f7;
  --colorPrimaryOutline: #dff5f7;
  --colorButtonNormal: #00b3bb;
  --colorButtonHover: #00cdd7;
  --colorLinkNormal: #00979f;
  --colorLinkHover: #00cdd7;
}

body {
  margin: 24px;
}


.upload_dropZone {
  color: #0f3c4b;
  background-color: var(--colorPrimaryPale, #c8dadf);
  outline: 2px dashed var(--colorPrimaryHalf, #c1ddef);
  outline-offset: -12px;
  transition:
    outline-offset 0.2s ease-out,
    outline-color 0.3s ease-in-out,
    background-color 0.2s ease-out;
}
.upload_dropZone.highlight {
  outline-offset: -4px;
  outline-color: var(--colorPrimaryNormal, #0576bd);
  background-color: var(--colorPrimaryEighth, #c8dadf);
}
.upload_svg {
  fill: var(--colorPrimaryNormal, #0576bd);
}
.btn-upload {
  color: #fff;
  background-color: var(--colorPrimaryNormal);
}
.btn-upload:hover,
.btn-upload:focus {
  color: #fff;
  background-color: var(--colorPrimaryGlare);
}
.upload_img {
  width: calc(33.333% - (2rem / 3));
  object-fit: contain;
}
</style>
@endsection

