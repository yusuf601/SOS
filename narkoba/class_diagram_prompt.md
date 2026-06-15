# Prompt & Kode UML Class Diagram (EduLab UHO)

Dokumen ini berisi deskripsi instruksi (prompt) dan kode diagram UML (PlantUML & Mermaid) yang merepresentasikan seluruh arsitektur class di backend **EduLab UHO**. Anda dapat langsung menyalin kode di bawah ini dan mengimpornya ke **Visual Paradigm** (melalui fitur *Import PlantUML*) atau generator diagram AI lainnya.

---

## 1. Prompt untuk AI Diagram Generator (Visual Paradigm / Chat/DLL)
> "Buatlah sebuah UML Class Diagram untuk aplikasi web berbasis PHP MVC Native bernama **EduLab UHO** (Platform Praktikum & Penilaian). Diagram harus mencakup kelas koneksi database, kelas model, dan kelas controller dengan relasi Dependency atau Association yang tepat. Gunakan format standar UML (atribut diawali `-` untuk private, `+` untuk public, tipe data dipisahkan dengan titik dua `:`):
> 
> **Kelas-Kelas yang Harus Ada:**
> 1. `Database` (Helper)
> 2. **Model:** `UserModel`, `KelasModel`, `ModulModel`, `TugasModel`
> 3. **Controller:** `AuthController`, `DashboardController`, `ModulController`, `TugasController`
> 
> **Atribut dan Method masing-masing kelas:**
> (Cantumkan seluruh method dan tipe data seperti yang tertulis dalam kode PlantUML di bawah)."

---

## 2. Kode PlantUML (Direkomendasikan untuk Visual Paradigm)
*Salin kode di bawah ini, lalu di Visual Paradigm pilih: **File > Import > PlantUML...***

```plantuml
@startuml
skinparam classAttributeIconSize 0
skinparam theme plain

class Database {
    - conn: PDO
    + getConnection(): PDO
}

class UserModel {
    - db: PDO
    - table: String = "Tabel_User"
    + __construct()
    + getUserById(id: int): array|false
    + getUserByUsername(username: String): array|false
    + verifyPassword(username: String, password: String): array|false
    + getAttendanceRate(userId: int): float
    + getAvailableRoles(userId: int, currentRole: String): array
}

class KelasModel {
    - db: PDO
    + __construct()
    + getAllClasses(): array
    + getClassById(id: int): array|false
    + getClassesByUserId(userId: int): array
    + getStudentClass(userId: int): array|false
}

class ModulModel {
    - db: PDO
    + __construct()
    + getAllModuls(): array
    + getModulById(id: int): array|false
}

class TugasModel {
    - db: PDO
    + __construct()
    + getAllTugas(): array
    + getTugasByModulId(modulId: int): array
    + getTugasById(id: int): array|false
    + getSubmission(tugasId: int, userId: int): array|false
    + submitTugas(tugasId: int, userId: int, fileTugas: String): boolean
    + getGradeForSubmission(pengumpulanId: int): array|false
    + getStudentProgress(userId: int): array
    + saveGrade(pengumpulanId: int, asistenId: int, nilai: float, feedback: String, status: String): boolean
}

class AuthController {
    - userModel: UserModel
    + __construct()
    + login()
    + logout()
    + switchRole()
}

class DashboardController {
    - userModel: UserModel
    - kelasModel: KelasModel
    - tugasModel: TugasModel
    + __construct()
    + student()
    + dosen()
    + asisten()
}

class ModulController {
    - modulModel: ModulModel
    - kelasModel: KelasModel
    + __construct()
    + index()
    + download()
}

class TugasController {
    - tugasModel: TugasModel
    - kelasModel: KelasModel
    + __construct()
    + index()
    + submit()
    + cancelSubmit()
    + submitGrade()
    + submitPresensi()
    + submitSanggah()
    + respondSanggah()
    + kelulusan()
}

' Relasi Database Dependency
UserModel ..> Database : uses
KelasModel ..> Database : uses
ModulModel ..> Database : uses
TugasModel ..> Database : uses

' Relasi Controller ke Model (Association)
AuthController "1" --> "1" UserModel
DashboardController "1" --> "1" UserModel
DashboardController "1" --> "1" KelasModel
DashboardController "1" --> "1" TugasModel
ModulController "1" --> "1" ModulModel
ModulController "1" --> "1" KelasModel
TugasController "1" --> "1" TugasModel
TugasController "1" --> "1" KelasModel

@endum
```

---

## 3. Kode Mermaid.js (Untuk Preview Markdown / Github / AI)

```mermaid
classDiagram
    direction TB
    class Database {
        -conn: PDO
        +getConnection() PDO
    }

    class UserModel {
        -db: PDO
        -table: String
        +getUserById(int id) array
        +getUserByUsername(String username) array
        +verifyPassword(String username, String password) array
        +getAttendanceRate(int userId) float
        +getAvailableRoles(int userId, String currentRole) array
    }

    class KelasModel {
        -db: PDO
        +getAllClasses() array
        +getClassById(int id) array
        +getClassesByUserId(int userId) array
        +getStudentClass(int userId) array
    }

    class ModulModel {
        -db: PDO
        +getAllModuls() array
        +getModulById(int id) array
    }

    class TugasModel {
        -db: PDO
        +getAllTugas() array
        +getTugasByModulId(int modulId) array
        +getTugasById(int id) array
        +getSubmission(int tugasId, int userId) array
        +submitTugas(int tugasId, int userId, String fileTugas) boolean
        +getGradeForSubmission(int pengumpulanId) array
        +getStudentProgress(int userId) array
        +saveGrade(int pengumpulanId, int asistenId, float nilai, String feedback, String status) boolean
    }

    class AuthController {
        -userModel: UserModel
        +login() void
        +logout() void
        +switchRole() void
    }

    class DashboardController {
        -userModel: UserModel
        -kelasModel: KelasModel
        -tugasModel: TugasModel
        +student() void
        +dosen() void
        +asisten() void
    }

    class ModulController {
        -modulModel: ModulModel
        -kelasModel: KelasModel
        +index() void
        +download() void
    }

    class TugasController {
        -tugasModel: TugasModel
        -kelasModel: KelasModel
        +index() void
        +submit() void
        +cancelSubmit() void
        +submitGrade() void
        +submitPresensi() void
        +submitSanggah() void
        +respondSanggah() void
        +kelulusan() void
    }

    UserModel ..> Database : uses
    KelasModel ..> Database : uses
    ModulModel ..> Database : uses
    TugasModel ..> Database : uses

    AuthController --> UserModel
    DashboardController --> UserModel
    DashboardController --> KelasModel
    DashboardController --> TugasModel
    ModulController --> ModulModel
    ModulController --> KelasModel
    TugasController --> TugasModel
    TugasController --> KelasModel
```
