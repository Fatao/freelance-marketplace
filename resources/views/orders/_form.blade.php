<div class="mb-3">
    <label class="form-label fw-semibold">Название заказа <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $order->title ?? '') }}" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Описание задачи <span class="text-danger">*</span></label>
    <textarea name="description" rows="6"
              class="form-control @error('description') is-invalid @enderror"
              placeholder="Подробно опишите что нужно сделать..." required>{{ old('description', $order->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Категория</label>
        <select name="category_id" class="form-select">
            <option value="">Не выбрана</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ old('category_id', $order->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Формат оплаты</label>
        <select name="payment_format" class="form-select">
            <option value="fixed" {{ old('payment_format', $order->payment_format ?? '') === 'fixed' ? 'selected' : '' }}>Фиксированный</option>
            <option value="hourly" {{ old('payment_format', $order->payment_format ?? '') === 'hourly' ? 'selected' : '' }}>Почасовой</option>
            <option value="negotiable" {{ old('payment_format', $order->payment_format ?? '') === 'negotiable' ? 'selected' : '' }}>Договорной</option>
        </select>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-sm-4">
        <label class="form-label fw-semibold">Бюджет от (₽)</label>
        <input type="number" name="budget_min" class="form-control"
               value="{{ old('budget_min', $order->budget_min ?? '') }}">
    </div>
    <div class="col-sm-4">
        <label class="form-label fw-semibold">Бюджет до (₽)</label>
        <input type="number" name="budget_max" class="form-control"
               value="{{ old('budget_max', $order->budget_max ?? '') }}">
    </div>
    <div class="col-sm-4">
        <label class="form-label fw-semibold">Срок выполнения</label>
        <input type="date" name="deadline" class="form-control"
               value="{{ old('deadline', isset($order->deadline) ? $order->deadline->format('Y-m-d') : '') }}">
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Необходимые навыки</label>
    <div class="row g-2">
        @foreach($skills as $skill)
            <div class="col-sm-4 col-6">
                <div class="form-check">
                    <input type="checkbox" name="skills[]" value="{{ $skill->id }}"
                           class="form-check-input" id="skill_{{ $skill->id }}"
                           {{ in_array($skill->id, old('skills', $order->id ? $order->skills->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="skill_{{ $skill->id }}">{{ $skill->name }}</label>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Ссылки на материалы</label>
    <input type="text" name="links" class="form-control"
           value="{{ old('links', $order->links ?? '') }}" placeholder="https://...">
</div>