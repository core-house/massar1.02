<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cv;
class CvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing CVs
        Cv::truncate();

        // Sample CVs with realistic data
        $cvs = [
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@example.com',
                'phone' => '+966501234567',
                'country' => 'Saudi Arabia',
                'state' => 'Riyadh',
                'city' => 'Riyadh',
                'address' => 'King Fahd Road, Riyadh',
                'birth_date' => '1990-05-15',
                'gender' => 'male',
                'marital_status' => 'married',
                'nationality' => 'Saudi',
                'religion' => 'Islam',
                'summary' => 'Experienced software developer with 5+ years in web development using Laravel and Vue.js.',
                'skills' => 'Laravel, Vue.js, MySQL, Git, Docker, AWS',
                'experience' => 'Senior Developer at TechCorp (2020-2023)\n- Led development of 3 major web applications\n- Mentored junior developers\n- Implemented CI/CD pipelines',
                'education' => 'Bachelor of Computer Science\nKing Saud University (2014)',
                'projects' => 'E-commerce Platform (Laravel + Vue.js)\nHR Management System (Laravel)\nInventory Management System',
                'certifications' => 'AWS Certified Developer\nLaravel Certified Developer',
                'languages' => 'Arabic (Native), English (Fluent)',
                'interests' => 'Open Source Development, Reading Tech Blogs, Traveling',
                'references' => 'Dr. Mohammed Ali - Professor at KSU\nEng. Sarah Ahmed - Senior Developer at TechCorp',
                'cover_letter' => 'I am passionate about creating innovative web solutions and have a strong track record of delivering high-quality applications.',
                'portfolio' => 'https://ahmed-hassan.dev',
            ],
            [
                'name' => 'Fatima Al-Zahra',
                'email' => 'fatima.alzahra@example.com',
                'phone' => '+966502345678',
                'country' => 'Saudi Arabia',
                'state' => 'Jeddah',
                'city' => 'Jeddah',
                'address' => 'Corniche Road, Jeddah',
                'birth_date' => '1992-08-22',
                'gender' => 'female',
                'marital_status' => 'single',
                'nationality' => 'Saudi',
                'religion' => 'Islam',
                'summary' => 'Creative UI/UX designer with expertise in user-centered design and modern design tools.',
                'skills' => 'Figma, Adobe Creative Suite, Sketch, HTML/CSS, JavaScript, React',
                'experience' => 'UI/UX Designer at DesignStudio (2021-2023)\n- Designed user interfaces for 10+ mobile apps\n- Conducted user research and usability testing\n- Created design systems and style guides',
                'education' => 'Bachelor of Design\nPrincess Nourah University (2016)',
                'projects' => 'Banking App Redesign\nE-learning Platform UI\nRestaurant Ordering App',
                'certifications' => 'Google UX Design Certificate\nFigma Advanced Course',
                'languages' => 'Arabic (Native), English (Fluent), French (Intermediate)',
                'interests' => 'Art, Photography, Travel, Design Conferences',
                'references' => 'Prof. Aisha Mohammed - Design Department Head\nEng. Omar Khalil - Product Manager at DesignStudio',
                'cover_letter' => 'I believe in creating designs that not only look beautiful but also solve real user problems.',
                'portfolio' => 'https://fatima-designs.com',
            ],
            [
                'name' => 'Omar Abdullah',
                'email' => 'omar.abdullah@example.com',
                'phone' => '+966503456789',
                'country' => 'Saudi Arabia',
                'state' => 'Dammam',
                'city' => 'Dammam',
                'address' => 'King Khalid Street, Dammam',
                'birth_date' => '1988-12-10',
                'gender' => 'male',
                'marital_status' => 'married',
                'nationality' => 'Saudi',
                'religion' => 'Islam',
                'summary' => 'Project manager with 8+ years experience in IT project management and team leadership.',
                'skills' => 'Project Management, Agile/Scrum, Jira, Microsoft Project, Risk Management, Stakeholder Management',
                'experience' => 'Senior Project Manager at IT Solutions (2019-2023)\n- Managed 15+ IT projects worth $2M+\n- Led cross-functional teams of 20+ members\n- Improved project delivery time by 25%',
                'education' => 'MBA in Project Management\nKing Fahd University (2018)\nBachelor of Information Technology\nKing Fahd University (2012)',
                'projects' => 'ERP System Implementation\nCloud Migration Project\nDigital Transformation Initiative',
                'certifications' => 'PMP (Project Management Professional)\nPRINCE2 Practitioner\nScrum Master Certified',
                'languages' => 'Arabic (Native), English (Fluent)',
                'interests' => 'Leadership Development, Technology Trends, Golf',
                'references' => 'Dr. Khalid Al-Rashid - MBA Director\nEng. Hassan Mohammed - CTO at IT Solutions',
                'cover_letter' => 'I am committed to delivering projects on time and within budget while ensuring high quality standards.',
                'portfolio' => 'https://omar-pm.com',
            ],
        ];

        // Create the sample CVs
        foreach ($cvs as $cvData) {
            Cv::create($cvData);
        }

        // Generate additional fake CVs using Faker
        $faker = \Faker\Factory::create('ar_SA'); // Arabic locale for Saudi names

        $nationalities = ['Saudi', 'Egyptian', 'Jordanian', 'Lebanese', 'Syrian', 'Palestinian', 'Moroccan', 'Tunisian', 'Algerian', 'Iraqi'];
        $religions = ['Islam', 'Christianity', 'Judaism'];
        $genders = ['male', 'female'];
        $maritalStatuses = ['single', 'married', 'divorced', 'widowed'];
        $cities = ['Riyadh', 'Jeddah', 'Dammam', 'Mecca', 'Medina', 'Taif', 'Buraidah', 'Tabuk', 'Hail', 'Khamis Mushait'];
        $states = ['Riyadh', 'Makkah', 'Eastern Province', 'Asir', 'Tabuk', 'Hail', 'Qassim', 'Northern Borders', 'Jazan', 'Najran'];

        $jobTitles = [
            'Software Developer', 'Web Developer', 'Mobile Developer', 'UI/UX Designer', 'Project Manager',
            'Business Analyst', 'Data Analyst', 'Marketing Manager', 'Sales Manager', 'HR Manager',
            'Accountant', 'Financial Analyst', 'Operations Manager', 'Quality Assurance', 'DevOps Engineer',
            'System Administrator', 'Network Engineer', 'Database Administrator', 'Product Manager', 'Content Writer',
        ];

        $skills = [
            'Laravel, PHP, MySQL, JavaScript, Vue.js, Git',
            'React, Node.js, MongoDB, Express.js, Docker',
            'Python, Django, PostgreSQL, AWS, Linux',
            'Java, Spring Boot, Oracle, Microservices, Kubernetes',
            'C#, .NET, SQL Server, Azure, Entity Framework',
            'Figma, Adobe Creative Suite, Sketch, HTML/CSS',
            'Project Management, Agile/Scrum, Jira, Risk Management',
            'Data Analysis, Excel, Power BI, SQL, Statistics',
            'Digital Marketing, SEO, Google Analytics, Social Media',
            'Accounting, QuickBooks, Financial Analysis, Tax Planning',
        ];

        // Generate 20 additional fake CVs
        for ($i = 0; $i < 20; $i++) {
            $gender = $faker->randomElement($genders);
            $firstName = $gender === 'male' ? $faker->firstNameMale() : $faker->firstNameFemale();
            $lastName = $faker->lastName();

            Cv::create([
                'name' => $firstName.' '.$lastName,
                'email' => strtolower($firstName.'.'.$lastName.'@example.com'),
                'phone' => '+966'.$faker->numerify('########'),
                'country' => 'Saudi Arabia',
                'state' => $faker->randomElement($states),
                'city' => $faker->randomElement($cities),
                'address' => $faker->streetAddress(),
                'birth_date' => $faker->date('Y-m-d', '2000-01-01'),
                'gender' => $gender,
                'marital_status' => $faker->randomElement($maritalStatuses),
                'nationality' => $faker->randomElement($nationalities),
                'religion' => $faker->randomElement($religions),
                'summary' => 'Experienced '.$faker->randomElement($jobTitles).' with '.$faker->numberBetween(2, 10).'+ years of experience in the field.',
                'skills' => $faker->randomElement($skills),
                'experience' => $faker->randomElement($jobTitles).' at '.$faker->company().' ('.$faker->date('Y').'-'.$faker->date('Y').')\n- '.$faker->sentence().'\n- '.$faker->sentence().'\n- '.$faker->sentence(),
                'education' => 'Bachelor of '.$faker->randomElement(['Computer Science', 'Business Administration', 'Engineering', 'Marketing', 'Finance']).'\n'.$faker->randomElement(['King Saud University', 'King Fahd University', 'Princess Nourah University', 'King Abdulaziz University']).' ('.$faker->numberBetween(2010, 2020).')',
                'projects' => $faker->sentence().'\n'.$faker->sentence().'\n'.$faker->sentence(),
                'certifications' => $faker->randomElement(['AWS Certified', 'PMP Certified', 'Google Certified', 'Microsoft Certified']).'\n'.$faker->randomElement(['Laravel Certified', 'Scrum Master', 'Agile Certified']),
                'languages' => 'Arabic (Native), English ('.$faker->randomElement(['Fluent', 'Intermediate', 'Advanced']).')',
                'interests' => $faker->sentence(),
                'references' => 'Dr. '.$faker->name().' - '.$faker->jobTitle().'\nEng. '.$faker->name().' - '.$faker->jobTitle(),
                'cover_letter' => $faker->paragraphs(3, true),
                'portfolio' => 'https://'.strtolower($firstName.$lastName).'.com',
            ]);
        }

        $this->command->info('Created 23 CV records with sample data.');
    }
}
