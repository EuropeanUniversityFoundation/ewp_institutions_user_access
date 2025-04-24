# EWP Institutions User Access

Access control based on a user's Institution.

## Installation

Include the repository in your project's `composer.json` file:

    "repositories": [
        ...
        {
            "type": "vcs",
            "url": "https://github.com/EuropeanUniversityFoundation/ewp_institutions_user_access"
        }
    ],

Then you can require the package as usual:

    composer require euf/ewp_institutions_user_access

Finally, install the module:

    drush en ewp_institutions_user_access

## Overview

This module associates any content entity with an Institution (via an entity reference base field) and performs access checks based on a user's Institution and any Institution associated with the content entity.

**@TODO** elaborate on the specifics...
