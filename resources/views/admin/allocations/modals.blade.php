<!-- Modal آپلود Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" dir="rtl">
            <div class="modal-header text-end">
                <h5 class="modal-title" id="importModalLabel">آپلود فایل Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-end">
                <form action="{{ route('allocations.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 text-end">
                        <label for="excelFile" class="form-label">فایل Excel</label>
                        <input type="file" name="file" class="form-control" id="excelFile" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">آپلود</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">لغو</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Edit Allocation Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ویرایش تخصیص</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_id" name="id">

                    <div class="row mb-2">
                        <div class="col-md-2">
                            <label for="edit_row">ردیف</label>
                            <input type="text" class="form-control" id="edit_row" name="row">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_Shahrestan">شهرستان</label>
                            <input type="text" class="form-control" id="edit_Shahrestan" name="Shahrestan">
                        </div>
                        <div class="col-md-2">
                            <label for="edit_sal">سال</label>
                            <input type="text" class="form-control" id="edit_sal" name="sal">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_erja_picker">ارجاع</label>
                            <input type="text" class="form-control" id="edit_erja_picker">
                            <input type="hidden" id="edit_erja" name="erja">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="edit_code">کد</label>
                            <select class="form-select" id="edit_code" name="code">
                                <option value="">انتخاب کنید</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_Takhsis_group">گروه تخصیص</label>
                            <select class="form-select" id="edit_Takhsis_group" name="Takhsis_group">
                                <option value="">انتخاب کنید</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_kelace">کلکسیه</label>
                            <input type="text" class="form-control" id="edit_kelace" name="kelace">
                        </div>
                        <div class="col-md-3">
                            <label for="edit_motaghasi">متعاقصی</label>
                            <input type="text" class="form-control" id="edit_motaghasi" name="motaghasi">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="edit_masraf">مصرف</label>
                            <input type="text" class="form-control" id="edit_masraf" name="masraf">
                        </div>
                        <div class="col-md-3">
                            <label for="edit_q_m">Q_m</label>
                            <input type="text" class="form-control" id="edit_q_m" name="q_m">
                        </div>
                        <div class="col-md-3">
                            <label for="edit_V_m">V_m</label>
                            <input type="text" class="form-control" id="edit_V_m" name="V_m">
                        </div>
                        <div class="col-md-3">
                            <label for="edit_sum">جمع</label>
                            <input type="text" class="form-control" id="edit_sum" name="sum" readonly>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label for="edit_t_mosavvab">ت مصوب</label>
                            <input type="text" class="form-control" id="edit_t_mosavvab" name="t_mosavvab"
                                readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_baghi">باقی</label>
                            <input type="text" class="form-control" id="edit_baghi" name="baghi" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_mosavabat">مصوبات</label>
                            <input type="text" class="form-control" id="edit_mosavabat" name="mosavabat">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label for="edit_file_name">فایل</label>
                            <input type="text" class="form-control" id="edit_file_name" name="file_name"
                                readonly>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveEditBtn" class="btn btn-primary">ذخیره تغییرات</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
            </div>
        </div>
    </div>
</div>
