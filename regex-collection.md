# Regex Collection
Regex can be used for two purposes as one of two types, filters and format compliance checkers:

- [**Filters**](#Filters) are designed for actively scan each character to see whether it does (or doesn't) match the defined expression. This is useful for PHP's ```preg_replace()```.  
- [**Format Compliance**](#Format-Compliance) refers to checking if a piece of data fits the formatting specified in the expression. This implies that the entire string is examined as a single unit, returning either a "true" or "false" verdict on if it matches. PHP provides ```preg_match()``` for this.  
    These can be used for HTML5 "pattern" attributes for verification on form submission.

---
**Note:** Filtering can be difficult when dealing with different locales.
The expressions below are categorized based on whether they support International language or only standard English letters.

---
## Table of Content
1. [Regex Collection](#regex-collection)
    1. [Table of Content](#table-of-content)
    2. [Filters](#filters)
        1. [International Language Support](#international-language-support)
            1. [Text](#text)
            2. [Numbers and Symbols](#numbers-and-symbols)
            3. [Resources](#resources)
        2. [English Support Only](#english-support-only)
            1. [Text](#text-1)
            2. [Numbers and Symbols](#numbers-and-symbols-1)
    3. [Format Compliance](#format-compliance)
        1. [Email](#email)
        2. [Phone Numbers](#phone-numbers)
        3. [Dates](#dates)
        4. [Resources](#resources-1)
---

## Filters
**Note:** All filters include an inversed variant for matching the opposite of what is defined. This can be useful for using ```preg_replace()``` to remove unwanted characters.  

### International Language Support
**Note:** Not every Regular Expression parser supports Unicode.  
**Note:** ```/u``` specifies Unicode support.  
**Note:** When utilizing inverse expressions, ```\P``` is the preferred method over ```^\p``` for "tempered greedy tokens" being poor for [performance](https://stackoverflow.com/a/37343088).  

#### Text
```
All Letters
    Expression:
        [\p{L}]*/u
        [\p{Letter}]*/u
    Inverse Expression:
        [\P{L}]*/u
        [\P{Letter}]*/u
        [^\p{L}]*/u
        [^\p{Letter}]*/u

Lowercase Letters
    Expression:
        [\p{Ll}]*/u
        [\p{Lowercase_Letter}]*/u
    Inverse Expression:
        [\P{Ll}]*/u
        [\P{Lowercase_Letter}]*/u
        [^\p{Ll}]*/u
        [^\p{Lowercase_Letter}]*/u

Uppercase Letters
    Expression:
        [\p{Lu}]*/u
        [\p{Uppercase_Letter}]*/u
    Inverse Expression:
        [\P{Lu}]*/u
        [\P{Uppercase_Letter}]*/u
        [^\p{Lu}]*/u
        [^\p{Uppercase_Letter}]*/u
        
Lowercase Letters + Uppercase Letters + Space
    Expression:
        [\p{L} ]*/u
        [\p{Letter} ]*/u
    Inverse Expression:
        [\P{L} ]*/u
        [\P{Letter} ]*/u
        [^\p{L} ]*/u
        [^\p{Letter} ]*/u
```

#### Numbers and Symbols
```
Regular Numbers
    Description:
        Matches all regular Roman numbers.
    Expression:
        [\p{Nl}]*/u
        [\p{Letter_Number}]*/u
    Inverse Expression:
        [\P{Nl}]*/u
        [\P{Letter_Number}]*/u
        [^\p{Nl}]*/u
        [^\p{Letter_Number}]*/u
```

#### Resources
https://www.regular-expressions.info/unicode.html#category

### English Support Only

#### Text
```
All Letters:
    Expression:
        [a-zA-Z]*
    Inverse Expression:
        [^a-zA-Z]*

Lowercase Letters
    Expression:
        [a-z]*
    Inverse Expression:
        [^a-z]*

Uppercase Letters
    Expression:
        [A-Z]*
    Inverse Expression:
        [^A-Z]*

Lowercase Letters + Uppercase Letters + Space
    Expression:
        [a-zA-Z ]*
    Inverse Expression:
        [^a-zA-Z ]*
```

#### Numbers and Symbols
```
Regular Numbers
    Expression:
        [0-9]*
    Inverse Expression:
        [^0-9]*
```

## Format Compliance

### Email
Only the built in "email" pattern should be used from [w3.org](https://www.w3.org/TR/2012/WD-html-markup-20120329/input.email.html).
```
Expression:
    ^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$
Format:
    <BLANK>@<BLANK>.<BLANK>
```

### Phone Numbers

Found via [StackOverflow](https://stackoverflow.com/a/16699507).
```
International Phone Number:
    Description:
        Checks for formatted, and unformatted, 10 digit phone numbers optionally including a 1-3 digit country code.
        Note: The country code MUST have a preceding '+'.
    Expression:
        ^(\+\d{1,3}\s)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$
    Test Cases:
        0123456789 //Valid unformatted 10 digit.
        012.345.6789 //Valid formatted 10 digit.
        012-345-6789 //Valid formatted 10 digit.
        +10123456789 //Valid with country code.
        +1 0123456789 //Valid with country code.
        +12 0123456789 //Valid with country code.
        +123 0123456789 //Valid with country code.
        +1234 0123456789 //Invalid country code - Too long.
        1 0123456789 //Invalid country code - Missing preceding '+'.
        10123456789 //Invalid country code - Missing preceding '+'.

United States Phone Number:
    Description:
        Checks for formatted, and unformatted, 10 digit phone numbers optionally including a 1-2 digit USA country code (01 or 1).
        Note: The country code MUST have a preceding '+'.
    Expression:
        ^(\+0?1)?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$
    Test Cases:
        0123456789 //Valid unformatted 10 digit.
        012.345.6789 //Valid formatted 10 digit.
        012-345-6789 //Valid formatted 10 digit.
        +10123456789 //Valid with country code.
        +1 0123456789 //Valid with country code.
        +12 0123456789 //Invalid country code - Not USA code '01' or '1'.
        +123 0123456789 //Invalid country code - Not USA code '01' or '1'.
        +1234 0123456789 //Invalid country code - Too long.
        1 0123456789 //Invalid country code - Missing preceding '+'.
        10123456789 //Invalid country code - Missing preceding '+'.
```

### Dates
```
Full Date Validation (including leap years)
    Description:
        A complicated expression that verifies pretty much everything, including leap years. 
        Note: It only goes from year 0000 to 9999.
    Expression:
        ^(?:(?:(?:0?[13578]|1[02])(\/|-|\.)31)\1|(?:(?:0?[1,3-9]|1[0-2])(\/|-|\.)(?:29|30)\2))(?:(?:1[6-9]|[0-9]\d)?\d{2})$|^(?:0?2(\/|-|\.)29\3(?:(?:(?:1[6-9]|[0-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:(?:0?[1-9])|(?:1[0-2]))(\/|-|\.)(?:0?[1-9]|1\d|2[0-8])\4$
    Format:
        MM/DD/YYYY | MM/DD/YY
    Test Cases:
        //2 Valid matches expected.
        03/31/0123 //Valid date
        02/29/2020 //Valid leap year
        04/0/2020 //Invalid day value - Too low
        04/31/2020 //Invalid day value - Too high
        0/5/2020 //Invalid month value - Too low
        13/5/2020 //Invalid month value - Too high
        03/28/01 //Invalid year input - Not specific enough, often missing prefix
        03/28/012 //Invalid year input - Too few digits
        03/28/01234 //Invalid year input - Too many digits
        02/29/0123 //Invalid leap year

Simple Date (YYYY-MM-DD)
    Description:
        Simply checks that the data is formatted properly and that numbers are only being used. It also verifies that the months/days are within "possible" 
    Expression:
        ^\d{4}\-(0?[1-9]|1[012])\-(0?[1-9]|[12][0-9]|3[01])$
    Format:
        YYYY-MM-DD
```

### Resources  
https://www.html5pattern.com
