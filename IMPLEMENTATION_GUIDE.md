# Enhanced Recruitment System Implementation Guide

## Overview
This implementation adds a comprehensive questionnaire system and template-based document generation to the existing PPMS (Personnel Placement Management System). The system follows the requirements specification from the "new requirements" folder.

## What Has Been Implemented

### 1. Database Schema (`sql/migrations/007_questionnaire_system.sql`)
- **questionnaire_requests**: Main table for questionnaire requests with secure tokens
- **questionnaire_requirements**: Configurable requirements per questionnaire
- **questionnaire_responses**: Stores candidate answers with autosave
- **questionnaire_documents**: Document uploads with validation
- **document_templates**: Template management for CV generation
- **template_field_mappings**: Field mapping between database and templates
- **questionnaire_audit_log**: Complete audit trail
- **Enhanced candidates table**: Added recruitment_destination field
- **Dashboard views**: For admin overview and requirements tracking

### 2. Questionnaire System (`app/controllers/questionnaire.php`)
- **create_action**: Admin creates questionnaire requests with configurable requirements
- **view_action**: Detailed view of questionnaire status, documents, responses
- **list_action**: Filterable list of all questionnaire requests
- **send_link_action**: Mark questionnaire as sent and log action
- **regenerate_link_action**: Generate new secure token for expired/compromised links
- **request_correction_action**: Request corrections from candidates
- **dashboard_action**: Admin dashboard with statistics and pending actions

### 3. Public Questionnaire Interface (`app/controllers/public_questionnaire.php`)
- **start_action**: Candidate-facing questionnaire with mobile-friendly design
- **save_action**: AJAX autosave functionality for all form fields
- **upload_document_action**: Secure document upload with validation
- **submit_action**: Final submission with validation of required fields
- **delete_document_action**: Allow candidates to replace uploaded documents

### 4. Template Management (`app/controllers/templates.php`)
- **create_action**: Create new document templates
- **view_action**: View template details and field mappings
- **list_action**: Filterable template repository
- **add_mapping_action**: Map template fields to database sources
- **delete_mapping_action**: Remove field mappings
- **generate_document_action**: Generate documents from candidate data

### 5. Document Generation (`app/lib/document_generator.php`)
- **Lithuania CV**: Specialized format for Lithuanian recruitment
- **Kandidato Anketa**: Lithuanian candidate questionnaire format
- **Turkey CV**: Turkish recruitment format (placeholder)
- **Generic CV**: Standard international CV format
- **Field mapping system**: Dynamic data population from multiple sources

### 6. Views and Interface
- **questionnaire_form.php**: Admin interface for creating questionnaires
- **questionnaire_view.php**: Detailed questionnaire status view
- **questionnaire_list.php**: Filterable questionnaire list
- **questionnaire_dashboard.php**: Admin dashboard with statistics
- **public_questionnaire_start.php**: Mobile-friendly candidate interface
- **public_questionnaire_success.php**: Submission confirmation
- **public_questionnaire_error.php**: Error handling for missing requirements
- **templates_*.php**: Complete template management interface

## Key Features Implemented

### Security & Access Control
- ✅ Secure random tokens for questionnaire links
- ✅ Token validation and expiry checking
- ✅ Audit trail for all actions
- ✅ Role-based access control
- ✅ CSRF protection on all forms
- ✅ File upload validation

### Questionnaire System
- ✅ Configurable requirements per questionnaire
- ✅ Required vs optional document types
- ✅ Multi-step form with progress tracking
- ✅ Autosave functionality (every 2 seconds on field change)
- ✅ Mobile-responsive design
- ✅ Document upload with file type validation
- ✅ Link expiry and regeneration
- ✅ Status tracking throughout lifecycle

### Document Generation
- ✅ Template-based CV generation
- ✅ Field mapping system (candidate database, questionnaire, static)
- ✅ Multiple template types (Lithuania, Turkey, Generic, Kandidato Anketa)
- ✅ PDF generation using mPDF
- ✅ Dynamic data population

### Admin Features
- ✅ Dashboard with statistics and pending actions
- ✅ Filterable lists and search
- ✅ Requirements status tracking
- ✅ Audit log viewing
- ✅ Correction request system
- ✅ Link management (send, regenerate, revoke)

## Installation Steps

### 1. Run Database Migration
```bash
mysql -u your_user -p your_database < sql/migrations/007_questionnaire_system.sql
```

### 2. Update Router
The router has been updated in `index.php` to include the new controllers:
- `questionnaire` - Admin questionnaire management
- `public-questionnaire` - Candidate-facing interface
- `templates` - Template management

### 3. Configure Upload Directory
Ensure the upload directory exists and is writable:
```bash
mkdir -p uploads/questionnaire
chmod 755 uploads/questionnaire
```

### 4. Update Configuration
No configuration changes needed - uses existing `app/config.php` settings.

## Usage Guide

### For Admin Users

#### Creating a Questionnaire Request
1. Navigate to `/index.php?page=questionnaire&action=create`
2. Select candidate (optional) or leave blank for new candidates
3. Enter position and recruitment destination
4. Set link expiry time (1-168 hours)
5. Select required information and documents
6. Add instructions for the candidate
7. Click "Create Questionnaire Request"

#### Managing Questionnaires
1. View dashboard: `/index.php?page=questionnaire-dashboard`
2. View all requests: `/index.php?page=questionnaire`
3. Click "View" on any request to see details
4. Use "Send Link" to mark as sent
5. Use "Regenerate Link" if link expires
6. Use "Request Correction" if changes needed

#### Managing Templates
1. Navigate to `/index.php?page=templates`
2. Create new templates for different recruitment destinations
3. Add field mappings to connect template fields to database
4. Generate documents for candidates using templates

### For Candidates

#### Completing Questionnaire
1. Click the secure link provided by admin
2. Complete personal information section
3. Fill in employment history and education
4. Upload required documents
5. Review all information
6. Accept declaration and submit

#### Features
- **Autosave**: All changes saved automatically
- **Progress tracking**: See completion percentage
- **Mobile-friendly**: Works on phones and tablets
- **Document upload**: Upload from device or take photos
- **Resume capability**: Return to incomplete questionnaires

## Database Relationships

```
candidates (existing)
  ├── questionnaire_requests (new)
  │     ├── questionnaire_requirements (new)
  │     ├── questionnaire_responses (new)
  │     ├── questionnaire_documents (new)
  │     └── questionnaire_audit_log (new)
  └── recruitment_destination (new field)

document_templates (new)
  └── template_field_mappings (new)
```

## Security Considerations

1. **Token Security**: 256-bit random tokens for questionnaire access
2. **Expiry Management**: Automatic link invalidation after expiry time
3. **File Validation**: MIME type checking and size limits
4. **Audit Trail**: Complete logging of all system actions
5. **SQL Injection Prevention**: All queries use prepared statements
6. **CSRF Protection**: All forms include CSRF tokens
7. **Access Control**: Role-based permissions enforced

## Testing Checklist

### Admin Functions
- [ ] Create questionnaire request
- [ ] View questionnaire details
- [ ] Send questionnaire link
- [ ] Regenerate expired link
- [ ] Request corrections
- [ ] View dashboard statistics
- [ ] Filter questionnaire list
- [ ] Create document template
- [ ] Add field mappings
- [ ] Generate document from template

### Candidate Functions
- [ ] Access questionnaire via secure link
- [ ] Complete personal information
- [ ] Add work history
- [ ] Upload documents
- [ ] Experience autosave
- [ ] Submit questionnaire
- [ ] View confirmation
- [ ] Handle validation errors

### Security Testing
- [ ] Invalid token rejection
- [ ] Expired link rejection
- [ ] Submitted link rejection
- [ ] File upload validation
- [ ] CSRF protection
- [ ] Role-based access

## Future Enhancements

### Potential Improvements
1. **Email Integration**: Automatic email sending for questionnaire links
2. **SMS Notifications**: SMS reminders for incomplete questionnaires
3. **Advanced Templates**: Word document generation in addition to PDF
4. **Video Integration**: Video upload and processing (if requirements change)
5. **API Integration**: REST API for external system integration
6. **Advanced Analytics**: Completion rates, time-to-completion metrics
7. **Multi-language Support**: Interface localization
8. **Digital Signatures**: Electronic signature capture

### Performance Optimizations
1. **Caching**: Template and questionnaire response caching
2. **Database Indexing**: Additional indexes for frequent queries
3. **File Storage**: Cloud storage integration for documents
4. **Background Processing**: Asynchronous document generation

## Troubleshooting

### Common Issues

**Questionnaire link not working**
- Check if token is valid in database
- Verify expiry time has not passed
- Ensure questionnaire status is not 'submitted' or 'revoked'

**Document upload failing**
- Check file size (max 10MB)
- Verify file type is allowed
- Ensure upload directory is writable
- Check PHP upload_max_filesize setting

**Template generation errors**
- Verify field mappings are correct
- Check candidate data exists for mapped fields
- Ensure mPDF library is properly installed

**Autosave not working**
- Check browser console for JavaScript errors
- Verify token is valid
- Ensure network connectivity

## Support and Maintenance

### Regular Maintenance Tasks
1. Review and clean expired questionnaire requests
2. Archive old audit logs
3. Update template field mappings as needed
4. Monitor storage usage for uploaded documents
5. Review security logs for suspicious activity

### Backup Recommendations
1. Regular database backups including new tables
2. Backup uploaded documents directory
3. Keep template files in version control
4. Document any custom field mappings

## Conclusion

This implementation provides a comprehensive solution for the enhanced recruitment system requirements. The system is secure, user-friendly, and maintains data integrity while providing flexibility for different recruitment destinations and document types.

The modular design allows for easy extension and customization, while the existing PPMS architecture ensures compatibility with current systems.