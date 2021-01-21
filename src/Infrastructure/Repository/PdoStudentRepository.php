<?php


namespace PDOProject\Pdo\Infrastructure\Repository;

use PDOProject\Pdo\Domain\Model\Phone;
use PDO;
use PDOProject\Pdo\Domain\Model\Student;
use PDOProject\Pdo\Domain\Repository\StudentRepository;

class PdoStudentRepository implements StudentRepository
{
    private PDO $connection;

    public function __construct(PDO $_connection)
    {
        $this->connection = $_connection;
    }

    public function allStudents(): array
    {
        $studentsArray = $this->connection->query('SELECT * FROM students')->fetchAll(PDO::FETCH_ASSOC);

        return $this->buildStudentsArray($studentsArray);
    }

    public function buildStudentsArray(array $students): array
    {
        $studentsList = [];

        foreach($students as $student){
            $student = new Student(
                $student['id'],
                $student['name'],
                new \DateTimeImmutable($student['birth_date'])
            );
            array_push($studentsList, $student);
        }

        return $studentsList;
    }

    public function add(Student $student): bool
    {
        if(!$student->getId()){
            throw new \Exception('Student Id is invalid');
        }

        $insertQuery = 'INSERT INTO students(id, name, birth_date) VALUES(:id, :name, :birth_date)';

        $statement = $this->connection->prepare($insertQuery);

        if($statement === false){
            throw new \RuntimeException($this->connection->errorInfo()[2]);
        }

        $statement->bindValue('id', $student->getId(), PDO::PARAM_INT);
        $statement->bindValue('name',$student->getName(), PDO::PARAM_STR);
        $statement->bindValue('birth_date', $student->getAge(), PDO::PARAM_STR);

        return $statement->execute();
    }

    public function update(Student $student): bool
    {
        if(!$student->getId()){
            throw new \Exception('Student Id is invalid');
        }

        $updateQuery = 'UPDATE students SET name = :name, birth_date = :birth_date WHERE id = :id';

        $statement = $this->connection->prepare($updateQuery);
        $statement->bindValue('id', $student->getId(), PDO::PARAM_INT);
        $statement->bindValue('name', $student->getName(), PDO::PARAM_STR);
        $statement->bindValue('birth_date', $student->getAge(), PDO::PARAM_STR);

        if($statement->execute()){
            return true;
        }

        return false;
    }

    public function remove(Student $student): bool
    {
        $deleteQuery = 'DELETE FROM students WHERE id = :id';

        $statement = $this->connection->prepare($deleteQuery);
        $statement->bindValue('id', $student->getId(), PDO::PARAM_INT);

        if($statement->execute()){
            return true;
        }

        return false;
    }

    public function studentsWithPhones(): array
    {
        $students = [];

        $sql = 'SELECT students.id, students.name, students.birth_date, phones.id AS phone_id, phones.area_code, phones.number
                FROM students
                JOIN phones
                ON (students.id = phones.student_id)';

        $result = $this->connection->query($sql)->fetchAll();

        foreach($result as $row){
            if(!array_key_exists($row['id'],$students)){
                array_push($students, new Student(
                    $row['id'],
                    $row['name'],
                    $row['birth_date']
                ));
            }

            $phone = new Phone($row['phone_id'], $row['area_code'], $row['number']);

            $students[$row['id']]->addPhone($phone);
        }

        return $students;
    }
}