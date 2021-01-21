<?php


namespace PDOProject\Pdo\Domain\Repository;

use PDOProject\Pdo\Domain\Model\Student;

interface StudentRepository
{
    public function allStudents(): array;
    public function studentsWithPhones(): array;
    public function add(Student $student): bool;
    public function update(Student $student): bool;
    public function remove(Student $student): bool;
}