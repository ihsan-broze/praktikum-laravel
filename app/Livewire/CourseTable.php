<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Course;

class CourseTable extends Component
{
    public $title, $description, $price, $courseId;
    public $isEdit = false;

    protected $rules = [
        'title' => 'required',
        'description' => 'required',
        'price' => 'required|numeric'
    ];

    public function save()
    {
        $this->validate();

        if ($this->isEdit) {
            Course::find($this->courseId)->update([
                'title' => $this->title,
                'description' => $this->description,
                'price' => $this->price
            ]);
            session()->flash('message', 'Course berhasil diperbarui!');
        } else {
            Course::create([
                'title' => $this->title,
                'description' => $this->description,
                'price' => $this->price
            ]);
            session()->flash('message', 'Course berhasil ditambahkan!');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $course = Course::find($id);
        $this->title = $course->title;
        $this->description = $course->description;
        $this->price = $course->price;
        $this->courseId = $course->id;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        Course::destroy($id);
        session()->flash('message', 'Course berhasil dihapus!');
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->price = '';
        $this->isEdit = false;
        $this->courseId = null;
    }

    public function render()
    {
        return view('livewire.course-table', [
            'courses' => Course::latest()->get()
        ]);
    }
}
