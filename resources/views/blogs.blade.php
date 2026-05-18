@extends('layouts.front')

@section('content')
    <!-- main-content start -->
    <section id="blog">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-11">
                    <h1 class="text-center fw-bold pt-3 text-theme">Blogs</h1>
                </div>
                <div class="col-1">
                    <!-- modal trigger for search -->
                    <button type="button" data-bs-toggle="modal" data-bs-target="#searchModal" class="btn float-end btn-theme mt-3"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <!-- Modal itself for search -->
            <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        {{-- <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div> --}}
                        <div class="modal-body">
                            <h1 class="fs-5 fw-bold text-center pb-2">Search Blogs</h1>
                            {{-- <div class=" d-flex "> --}}
                                {{-- <label for="search" class="form-label mt-1"><i class="bi bi-search "></i></label> --}}
                                <input type="text" class="form-control bg-grey form-control-css border-secondary rounded"
                                    placeholder="Enter keyword..." id="search">
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <section id="SearchResult">
                <div class="row px-3 py-4">
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="card border-css">
                            <a href="blog-post" class="text-decoration-none link-dark">
                                <img src="assets/images/mole-removal.png" class="card-img-top px-3 pt-3" alt="Image">
                                <div class="card-body">
                                    <span class="badge bg-theme p-2">Moles</span>
                                    <span class="badge bg-theme p-2">Mole Removal</span>
                                    <h5 class="card-title fw-bold pt-2">What is Mole and removal procedure.</h5>
                                    <p class="card-text text-truncate">Lorem ipsum dolor sit amet consectetur adipisicing
                                        elit. Rem, nihil.</p>
                                </div>
                            </a>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </section>
    <!-- main-content end -->
@endsection
