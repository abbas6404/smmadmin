# SMM Admin API Documentation

## Authentication

All API endpoints require authentication using a Bearer token in the Authorization header:
```
Authorization: Bearer <access_token>
```

## Endpoints

### 1. Generate Token
Generate an access token for PC profile authentication.

**Endpoint:** `POST /api/generate-token`

**Request Body:**
```json
{
    "email": "example@email.com",
    "password": "your_password",
    "pc_name": "DESKTOP-ABC123",
    "hostname": "user-pc",
    "os_version": "Windows 10",
    "hardware_id": "xyz-123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Access token generated successfully",
    "data": {
        "access_token": "generated_token",
        "pc_profile": {
            "id": 1,
            "email": "example@email.com",
            "pc_name": "DESKTOP-ABC123",
            "hostname": "user-pc",
            "os_version": "Windows 10",
            "hardware_id": "xyz-123",
            "status": "active",
            "last_verified_at": "2024-04-21T12:00:00Z"
        }
    }
}
```

### 2. Get/Update Profile
Get PC profile information or update system information.

**Endpoint:** `GET/POST /api/get-profile`

**GET Request:**
- No request body required
- Returns current profile information

**POST Request Body:**
```json
{
    "cpu_cores": 8,
    "total_memory": 16384,
    "disks": [
        {
            "drive_letter": "C",
            "file_system": "NTFS",
            "total_size": 0.25,      // Size in GB (e.g., 0.25 GB)
            "free_space": 0.15,      // Size in GB (e.g., 0.15 GB)
            "used_space": 0.10,      // Size in GB (e.g., 0.10 GB)
            "health_percentage": 98.5,
            "read_speed": 550,
            "write_speed": 520
        }
    ]
}
```

**Response (Both GET and POST):**
```json
{
    "success": true,
    "message": "PC profile information retrieved/updated successfully",
    "data": {
        "pc_profile": {
            "id": 1,
            "email": "example@email.com",
            "pc_name": "DESKTOP-ABC123",
            "hostname": "user-pc",
            "os_version": "Windows 10",
            "hardware_id": "xyz-123",
            "user_agent": "Mozilla/5.0...",
            "profile_root_directory": "/path/to/profile",
            "status": "active",
            "last_verified_at": "2024-04-21T12:00:00Z",
            "limits": {
                "max_profile_limit": 10,
                "max_order_limit": 100,
                "min_order_limit": 1
            },
            "system_info": {
                "cpu_cores": 8,
                "total_memory": 16384
            },
            "disks": [
                {
                    "drive_letter": "C",
                    "file_system": "NTFS",
                    "total_size": 0.25,      // Size in GB
                    "free_space": 0.15,      // Size in GB
                    "used_space": 0.10,      // Size in GB
                    "health_percentage": 98.5,
                    "read_speed": 550,
                    "write_speed": 520,
                    "last_checked_at": "2024-04-21T12:00:00Z"
                }
            ]
        }
    }
}
```

## Notes

1. **Disk Size Handling:**
   - All disk sizes (total_size, free_space, used_space) are handled in GB
   - Values are stored in bytes in the database
   - API accepts and returns values in GB
   - Values are rounded to 2 decimal places in responses

2. **Optional Fields:**
   - All fields in the POST request are optional
   - Only provided fields will be updated
   - drive_letter is required only when providing disk information

3. **Error Responses:**
   ```json
   {
       "success": false,
       "message": "Error message",
       "errors": {
           "field_name": ["Error description"]
       }
   }
   ```

4. **HTTP Status Codes:**
   - 200: Success
   - 401: Unauthorized (invalid or missing token)
   - 403: Forbidden (inactive or blocked profile)
   - 422: Validation error
   - 500: Server error 