<?php
//
//
//
//
//
//
//
class TCPDF_STATIC {
	private static $tcpdf_version = '6.2.11';
	public static $alias_tot_pages = '{:ptp:}';
	public static $alias_num_page = '{:pnp:}';
	public static $alias_group_tot_pages = '{:ptg:}';
	public static $alias_group_num_page = '{:png:}';
	public static $alias_right_shift = '{rsc:';
	public static $enc_padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
	public static $byterange_string = '/ByteRange[0 ********** ********** **********]';
	public static $pageboxes = array('MediaBox', 'CropBox', 'BleedBox', 'TrimBox', 'ArtBox');
	public static $page_formats = array(
		'A0'                     => array( 2383.937,  3370.394), // = (  841 x 1189 ) mm  = ( 33.11 x 46.81 ) in
		'A1'                     => array( 1683.780,  2383.937), // = (  594 x 841  ) mm  = ( 23.39 x 33.11 ) in
		'A2'                     => array( 1190.551,  1683.780), // = (  420 x 594  ) mm  = ( 16.54 x 23.39 ) in
		'A3'                     => array(  841.890,  1190.551), // = (  297 x 420  ) mm  = ( 11.69 x 16.54 ) in
		'A4'                     => array(  595.276,   841.890), // = (  210 x 297  ) mm  = (  8.27 x 11.69 ) in
		'A5'                     => array(  419.528,   595.276), // = (  148 x 210  ) mm  = (  5.83 x 8.27  ) in
		'A6'                     => array(  297.638,   419.528), // = (  105 x 148  ) mm  = (  4.13 x 5.83  ) in
		'A7'                     => array(  209.764,   297.638), // = (   74 x 105  ) mm  = (  2.91 x 4.13  ) in
		'A8'                     => array(  147.402,   209.764), // = (   52 x 74   ) mm  = (  2.05 x 2.91  ) in
		'A9'                     => array(  104.882,   147.402), // = (   37 x 52   ) mm  = (  1.46 x 2.05  ) in
		'A10'                    => array(   73.701,   104.882), // = (   26 x 37   ) mm  = (  1.02 x 1.46  ) in
		'A11'                    => array(   51.024,    73.701), // = (   18 x 26   ) mm  = (  0.71 x 1.02  ) in
		'A12'                    => array(   36.850,    51.024), // = (   13 x 18   ) mm  = (  0.51 x 0.71  ) in
		'B0'                     => array( 2834.646,  4008.189), // = ( 1000 x 1414 ) mm  = ( 39.37 x 55.67 ) in
		'B1'                     => array( 2004.094,  2834.646), // = (  707 x 1000 ) mm  = ( 27.83 x 39.37 ) in
		'B2'                     => array( 1417.323,  2004.094), // = (  500 x 707  ) mm  = ( 19.69 x 27.83 ) in
		'B3'                     => array( 1000.630,  1417.323), // = (  353 x 500  ) mm  = ( 13.90 x 19.69 ) in
		'B4'                     => array(  708.661,  1000.630), // = (  250 x 353  ) mm  = (  9.84 x 13.90 ) in
		'B5'                     => array(  498.898,   708.661), // = (  176 x 250  ) mm  = (  6.93 x 9.84  ) in
		'B6'                     => array(  354.331,   498.898), // = (  125 x 176  ) mm  = (  4.92 x 6.93  ) in
		'B7'                     => array(  249.449,   354.331), // = (   88 x 125  ) mm  = (  3.46 x 4.92  ) in
		'B8'                     => array(  175.748,   249.449), // = (   62 x 88   ) mm  = (  2.44 x 3.46  ) in
		'B9'                     => array(  124.724,   175.748), // = (   44 x 62   ) mm  = (  1.73 x 2.44  ) in
		'B10'                    => array(   87.874,   124.724), // = (   31 x 44   ) mm  = (  1.22 x 1.73  ) in
		'B11'                    => array(   62.362,    87.874), // = (   22 x 31   ) mm  = (  0.87 x 1.22  ) in
		'B12'                    => array(   42.520,    62.362), // = (   15 x 22   ) mm  = (  0.59 x 0.87  ) in
		'C0'                     => array( 2599.370,  3676.535), // = (  917 x 1297 ) mm  = ( 36.10 x 51.06 ) in
		'C1'                     => array( 1836.850,  2599.370), // = (  648 x 917  ) mm  = ( 25.51 x 36.10 ) in
		'C2'                     => array( 1298.268,  1836.850), // = (  458 x 648  ) mm  = ( 18.03 x 25.51 ) in
		'C3'                     => array(  918.425,  1298.268), // = (  324 x 458  ) mm  = ( 12.76 x 18.03 ) in
		'C4'                     => array(  649.134,   918.425), // = (  229 x 324  ) mm  = (  9.02 x 12.76 ) in
		'C5'                     => array(  459.213,   649.134), // = (  162 x 229  ) mm  = (  6.38 x 9.02  ) in
		'C6'                     => array(  323.150,   459.213), // = (  114 x 162  ) mm  = (  4.49 x 6.38  ) in
		'C7'                     => array(  229.606,   323.150), // = (   81 x 114  ) mm  = (  3.19 x 4.49  ) in
		'C8'                     => array(  161.575,   229.606), // = (   57 x 81   ) mm  = (  2.24 x 3.19  ) in
		'C9'                     => array(  113.386,   161.575), // = (   40 x 57   ) mm  = (  1.57 x 2.24  ) in
		'C10'                    => array(   79.370,   113.386), // = (   28 x 40   ) mm  = (  1.10 x 1.57  ) in
		'C11'                    => array(   56.693,    79.370), // = (   20 x 28   ) mm  = (  0.79 x 1.10  ) in
		'C12'                    => array(   39.685,    56.693), // = (   14 x 20   ) mm  = (  0.55 x 0.79  ) in
		'C76'                    => array(  229.606,   459.213), // = (   81 x 162  ) mm  = (  3.19 x 6.38  ) in
		'DL'                     => array(  311.811,   623.622), // = (  110 x 220  ) mm  = (  4.33 x 8.66  ) in
		'DLE'                    => array(  323.150,   637.795), // = (  114 x 225  ) mm  = (  4.49 x 8.86  ) in
		'DLX'                    => array(  340.158,   666.142), // = (  120 x 235  ) mm  = (  4.72 x 9.25  ) in
		'DLP'                    => array(  280.630,   595.276), // = (   99 x 210  ) mm  = (  3.90 x 8.27  ) in (1/3 A4)
		'E0'                     => array( 2491.654,  3517.795), // = (  879 x 1241 ) mm  = ( 34.61 x 48.86 ) in
		'E1'                     => array( 1757.480,  2491.654), // = (  620 x 879  ) mm  = ( 24.41 x 34.61 ) in
		'E2'                     => array( 1247.244,  1757.480), // = (  440 x 620  ) mm  = ( 17.32 x 24.41 ) in
		'E3'                     => array(  878.740,  1247.244), // = (  310 x 440  ) mm  = ( 12.20 x 17.32 ) in
		'E4'                     => array(  623.622,   878.740), // = (  220 x 310  ) mm  = (  8.66 x 12.20 ) in
		'E5'                     => array(  439.370,   623.622), // = (  155 x 220  ) mm  = (  6.10 x 8.66  ) in
		'E6'                     => array(  311.811,   439.370), // = (  110 x 155  ) mm  = (  4.33 x 6.10  ) in
		'E7'                     => array(  221.102,   311.811), // = (   78 x 110  ) mm  = (  3.07 x 4.33  ) in
		'E8'                     => array(  155.906,   221.102), // = (   55 x 78   ) mm  = (  2.17 x 3.07  ) in
		'E9'                     => array(  110.551,   155.906), // = (   39 x 55   ) mm  = (  1.54 x 2.17  ) in
		'E10'                    => array(   76.535,   110.551), // = (   27 x 39   ) mm  = (  1.06 x 1.54  ) in
		'E11'                    => array(   53.858,    76.535), // = (   19 x 27   ) mm  = (  0.75 x 1.06  ) in
		'E12'                    => array(   36.850,    53.858), // = (   13 x 19   ) mm  = (  0.51 x 0.75  ) in
		'G0'                     => array( 2715.591,  3838.110), // = (  958 x 1354 ) mm  = ( 37.72 x 53.31 ) in
		'G1'                     => array( 1919.055,  2715.591), // = (  677 x 958  ) mm  = ( 26.65 x 37.72 ) in
		'G2'                     => array( 1357.795,  1919.055), // = (  479 x 677  ) mm  = ( 18.86 x 26.65 ) in
		'G3'                     => array(  958.110,  1357.795), // = (  338 x 479  ) mm  = ( 13.31 x 18.86 ) in
		'G4'                     => array(  677.480,   958.110), // = (  239 x 338  ) mm  = (  9.41 x 13.31 ) in
		'G5'                     => array(  479.055,   677.480), // = (  169 x 239  ) mm  = (  6.65 x 9.41  ) in
		'G6'                     => array(  337.323,   479.055), // = (  119 x 169  ) mm  = (  4.69 x 6.65  ) in
		'G7'                     => array(  238.110,   337.323), // = (   84 x 119  ) mm  = (  3.31 x 4.69  ) in
		'G8'                     => array(  167.244,   238.110), // = (   59 x 84   ) mm  = (  2.32 x 3.31  ) in
		'G9'                     => array(  119.055,   167.244), // = (   42 x 59   ) mm  = (  1.65 x 2.32  ) in
		'G10'                    => array(   82.205,   119.055), // = (   29 x 42   ) mm  = (  1.14 x 1.65  ) in
		'G11'                    => array(   59.528,    82.205), // = (   21 x 29   ) mm  = (  0.83 x 1.14  ) in
		'G12'                    => array(   39.685,    59.528), // = (   14 x 21   ) mm  = (  0.55 x 0.83  ) in
		'RA0'                    => array( 2437.795,  3458.268), // = (  860 x 1220 ) mm  = ( 33.86 x 48.03 ) in
		'RA1'                    => array( 1729.134,  2437.795), // = (  610 x 860  ) mm  = ( 24.02 x 33.86 ) in
		'RA2'                    => array( 1218.898,  1729.134), // = (  430 x 610  ) mm  = ( 16.93 x 24.02 ) in
		'RA3'                    => array(  864.567,  1218.898), // = (  305 x 430  ) mm  = ( 12.01 x 16.93 ) in
		'RA4'                    => array(  609.449,   864.567), // = (  215 x 305  ) mm  = (  8.46 x 12.01 ) in
		'SRA0'                   => array( 2551.181,  3628.346), // = (  900 x 1280 ) mm  = ( 35.43 x 50.39 ) in
		'SRA1'                   => array( 1814.173,  2551.181), // = (  640 x 900  ) mm  = ( 25.20 x 35.43 ) in
		'SRA2'                   => array( 1275.591,  1814.173), // = (  450 x 640  ) mm  = ( 17.72 x 25.20 ) in
		'SRA3'                   => array(  907.087,  1275.591), // = (  320 x 450  ) mm  = ( 12.60 x 17.72 ) in
		'SRA4'                   => array(  637.795,   907.087), // = (  225 x 320  ) mm  = (  8.86 x 12.60 ) in
		'4A0'                    => array( 4767.874,  6740.787), // = ( 1682 x 2378 ) mm  = ( 66.22 x 93.62 ) in
		'2A0'                    => array( 3370.394,  4767.874), // = ( 1189 x 1682 ) mm  = ( 46.81 x 66.22 ) in
		'A2_EXTRA'               => array( 1261.417,  1754.646), // = (  445 x 619  ) mm  = ( 17.52 x 24.37 ) in
		'A3+'                    => array(  932.598,  1369.134), // = (  329 x 483  ) mm  = ( 12.95 x 19.02 ) in
		'A3_EXTRA'               => array(  912.756,  1261.417), // = (  322 x 445  ) mm  = ( 12.68 x 17.52 ) in
		'A3_SUPER'               => array(  864.567,  1440.000), // = (  305 x 508  ) mm  = ( 12.01 x 20.00 ) in
		'SUPER_A3'               => array(  864.567,  1380.472), // = (  305 x 487  ) mm  = ( 12.01 x 19.17 ) in
		'A4_EXTRA'               => array(  666.142,   912.756), // = (  235 x 322  ) mm  = (  9.25 x 12.68 ) in
		'A4_SUPER'               => array(  649.134,   912.756), // = (  229 x 322  ) mm  = (  9.02 x 12.68 ) in
		'SUPER_A4'               => array(  643.465,  1009.134), // = (  227 x 356  ) mm  = (  8.94 x 14.02 ) in
		'A4_LONG'                => array(  595.276,   986.457), // = (  210 x 348  ) mm  = (  8.27 x 13.70 ) in
		'F4'                     => array(  595.276,   935.433), // = (  210 x 330  ) mm  = (  8.27 x 12.99 ) in
		'SO_B5_EXTRA'            => array(  572.598,   782.362), // = (  202 x 276  ) mm  = (  7.95 x 10.87 ) in
		'A5_EXTRA'               => array(  490.394,   666.142), // = (  173 x 235  ) mm  = (  6.81 x 9.25  ) in
		'ANSI_E'                 => array( 2448.000,  3168.000), // = (  864 x 1118 ) mm  = ( 34.00 x 44.00 ) in
		'ANSI_D'                 => array( 1584.000,  2448.000), // = (  559 x 864  ) mm  = ( 22.00 x 34.00 ) in
		'ANSI_C'                 => array( 1224.000,  1584.000), // = (  432 x 559  ) mm  = ( 17.00 x 22.00 ) in
		'ANSI_B'                 => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
		'ANSI_A'                 => array(  612.000,   792.000), // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
		'USLEDGER'               => array( 1224.000,   792.000), // = (  432 x 279  ) mm  = ( 17.00 x 11.00 ) in
		'LEDGER'                 => array( 1224.000,   792.000), // = (  432 x 279  ) mm  = ( 17.00 x 11.00 ) in
		'ORGANIZERK'             => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
		'BIBLE'                  => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
		'USTABLOID'              => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
		'TABLOID'                => array(  792.000,  1224.000), // = (  279 x 432  ) mm  = ( 11.00 x 17.00 ) in
		'ORGANIZERM'             => array(  612.000,   792.000), // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
		'USLETTER'               => array(  612.000,   792.000), // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
		'LETTER'                 => array(  612.000,   792.000), // = (  216 x 279  ) mm  = (  8.50 x 11.00 ) in
		'USLEGAL'                => array(  612.000,  1008.000), // = (  216 x 356  ) mm  = (  8.50 x 14.00 ) in
		'LEGAL'                  => array(  612.000,  1008.000), // = (  216 x 356  ) mm  = (  8.50 x 14.00 ) in
		'GOVERNMENTLETTER'       => array(  576.000,   756.000), // = (  203 x 267  ) mm  = (  8.00 x 10.50 ) in
		'GLETTER'                => array(  576.000,   756.000), // = (  203 x 267  ) mm  = (  8.00 x 10.50 ) in
		'JUNIORLEGAL'            => array(  576.000,   360.000), // = (  203 x 127  ) mm  = (  8.00 x 5.00  ) in
		'JLEGAL'                 => array(  576.000,   360.000), // = (  203 x 127  ) mm  = (  8.00 x 5.00  ) in
		'QUADDEMY'               => array( 2520.000,  3240.000), // = (  889 x 1143 ) mm  = ( 35.00 x 45.00 ) in
		'SUPER_B'                => array(  936.000,  1368.000), // = (  330 x 483  ) mm  = ( 13.00 x 19.00 ) in
		'QUARTO'                 => array(  648.000,   792.000), // = (  229 x 279  ) mm  = (  9.00 x 11.00 ) in
		'GOVERNMENTLEGAL'        => array(  612.000,   936.000), // = (  216 x 330  ) mm  = (  8.50 x 13.00 ) in
		'FOLIO'                  => array(  612.000,   936.000), // = (  216 x 330  ) mm  = (  8.50 x 13.00 ) in
		'MONARCH'                => array(  522.000,   756.000), // = (  184 x 267  ) mm  = (  7.25 x 10.50 ) in
		'EXECUTIVE'              => array(  522.000,   756.000), // = (  184 x 267  ) mm  = (  7.25 x 10.50 ) in
		'ORGANIZERL'             => array(  396.000,   612.000), // = (  140 x 216  ) mm  = (  5.50 x 8.50  ) in
		'STATEMENT'              => array(  396.000,   612.000), // = (  140 x 216  ) mm  = (  5.50 x 8.50  ) in
		'MEMO'                   => array(  396.000,   612.000), // = (  140 x 216  ) mm  = (  5.50 x 8.50  ) in
		'FOOLSCAP'               => array(  595.440,   936.000), // = (  210 x 330  ) mm  = (  8.27 x 13.00 ) in
		'COMPACT'                => array(  306.000,   486.000), // = (  108 x 171  ) mm  = (  4.25 x 6.75  ) in
		'ORGANIZERJ'             => array(  198.000,   360.000), // = (   70 x 127  ) mm  = (  2.75 x 5.00  ) in
		'P1'                     => array( 1587.402,  2437.795), // = (  560 x 860  ) mm  = ( 22.05 x 33.86 ) in
		'P2'                     => array( 1218.898,  1587.402), // = (  430 x 560  ) mm  = ( 16.93 x 22.05 ) in
		'P3'                     => array(  793.701,  1218.898), // = (  280 x 430  ) mm  = ( 11.02 x 16.93 ) in
		'P4'                     => array(  609.449,   793.701), // = (  215 x 280  ) mm  = (  8.46 x 11.02 ) in
		'P5'                     => array(  396.850,   609.449), // = (  140 x 215  ) mm  = (  5.51 x 8.46  ) in
		'P6'                     => array(  303.307,   396.850), // = (  107 x 140  ) mm  = (  4.21 x 5.51  ) in
		'ARCH_E'                 => array( 2592.000,  3456.000), // = (  914 x 1219 ) mm  = ( 36.00 x 48.00 ) in
		'ARCH_E1'                => array( 2160.000,  3024.000), // = (  762 x 1067 ) mm  = ( 30.00 x 42.00 ) in
		'ARCH_D'                 => array( 1728.000,  2592.000), // = (  610 x 914  ) mm  = ( 24.00 x 36.00 ) in
		'BROADSHEET'             => array( 1296.000,  1728.000), // = (  457 x 610  ) mm  = ( 18.00 x 24.00 ) in
		'ARCH_C'                 => array( 1296.000,  1728.000), // = (  457 x 610  ) mm  = ( 18.00 x 24.00 ) in
		'ARCH_B'                 => array(  864.000,  1296.000), // = (  305 x 457  ) mm  = ( 12.00 x 18.00 ) in
		'ARCH_A'                 => array(  648.000,   864.000), // = (  229 x 305  ) mm  = (  9.00 x 12.00 ) in
		'ANNENV_A2'              => array(  314.640,   414.000), // = (  111 x 146  ) mm  = (  4.37 x 5.75  ) in
		'ANNENV_A6'              => array(  342.000,   468.000), // = (  121 x 165  ) mm  = (  4.75 x 6.50  ) in
		'ANNENV_A7'              => array(  378.000,   522.000), // = (  133 x 184  ) mm  = (  5.25 x 7.25  ) in
		'ANNENV_A8'              => array(  396.000,   584.640), // = (  140 x 206  ) mm  = (  5.50 x 8.12  ) in
		'ANNENV_A10'             => array(  450.000,   692.640), // = (  159 x 244  ) mm  = (  6.25 x 9.62  ) in
		'ANNENV_SLIM'            => array(  278.640,   638.640), // = (   98 x 225  ) mm  = (  3.87 x 8.87  ) in
		'COMMENV_N6_1/4'         => array(  252.000,   432.000), // = (   89 x 152  ) mm  = (  3.50 x 6.00  ) in
		'COMMENV_N6_3/4'         => array(  260.640,   468.000), // = (   92 x 165  ) mm  = (  3.62 x 6.50  ) in
		'COMMENV_N8'             => array(  278.640,   540.000), // = (   98 x 191  ) mm  = (  3.87 x 7.50  ) in
		'COMMENV_N9'             => array(  278.640,   638.640), // = (   98 x 225  ) mm  = (  3.87 x 8.87  ) in
		'COMMENV_N10'            => array(  296.640,   684.000), // = (  105 x 241  ) mm  = (  4.12 x 9.50  ) in
		'COMMENV_N11'            => array(  324.000,   746.640), // = (  114 x 263  ) mm  = (  4.50 x 10.37 ) in
		'COMMENV_N12'            => array(  342.000,   792.000), // = (  121 x 279  ) mm  = (  4.75 x 11.00 ) in
		'COMMENV_N14'            => array(  360.000,   828.000), // = (  127 x 292  ) mm  = (  5.00 x 11.50 ) in
		'CATENV_N1'              => array(  432.000,   648.000), // = (  152 x 229  ) mm  = (  6.00 x 9.00  ) in
		'CATENV_N1_3/4'          => array(  468.000,   684.000), // = (  165 x 241  ) mm  = (  6.50 x 9.50  ) in
		'CATENV_N2'              => array(  468.000,   720.000), // = (  165 x 254  ) mm  = (  6.50 x 10.00 ) in
		'CATENV_N3'              => array(  504.000,   720.000), // = (  178 x 254  ) mm  = (  7.00 x 10.00 ) in
		'CATENV_N6'              => array(  540.000,   756.000), // = (  191 x 267  ) mm  = (  7.50 x 10.50 ) in
		'CATENV_N7'              => array(  576.000,   792.000), // = (  203 x 279  ) mm  = (  8.00 x 11.00 ) in
		'CATENV_N8'              => array(  594.000,   810.000), // = (  210 x 286  ) mm  = (  8.25 x 11.25 ) in
		'CATENV_N9_1/2'          => array(  612.000,   756.000), // = (  216 x 267  ) mm  = (  8.50 x 10.50 ) in
		'CATENV_N9_3/4'          => array(  630.000,   810.000), // = (  222 x 286  ) mm  = (  8.75 x 11.25 ) in
		'CATENV_N10_1/2'         => array(  648.000,   864.000), // = (  229 x 305  ) mm  = (  9.00 x 12.00 ) in
		'CATENV_N12_1/2'         => array(  684.000,   900.000), // = (  241 x 318  ) mm  = (  9.50 x 12.50 ) in
		'CATENV_N13_1/2'         => array(  720.000,   936.000), // = (  254 x 330  ) mm  = ( 10.00 x 13.00 ) in
		'CATENV_N14_1/4'         => array(  810.000,   882.000), // = (  286 x 311  ) mm  = ( 11.25 x 12.25 ) in
		'CATENV_N14_1/2'         => array(  828.000,  1044.000), // = (  292 x 368  ) mm  = ( 11.50 x 14.50 ) in
		'JIS_B0'                 => array( 2919.685,  4127.244), // = ( 1030 x 1456 ) mm  = ( 40.55 x 57.32 ) in
		'JIS_B1'                 => array( 2063.622,  2919.685), // = (  728 x 1030 ) mm  = ( 28.66 x 40.55 ) in
		'JIS_B2'                 => array( 1459.843,  2063.622), // = (  515 x 728  ) mm  = ( 20.28 x 28.66 ) in
		'JIS_B3'                 => array( 1031.811,  1459.843), // = (  364 x 515  ) mm  = ( 14.33 x 20.28 ) in
		'JIS_B4'                 => array(  728.504,  1031.811), // = (  257 x 364  ) mm  = ( 10.12 x 14.33 ) in
		'JIS_B5'                 => array(  515.906,   728.504), // = (  182 x 257  ) mm  = (  7.17 x 10.12 ) in
		'JIS_B6'                 => array(  362.835,   515.906), // = (  128 x 182  ) mm  = (  5.04 x 7.17  ) in
		'JIS_B7'                 => array(  257.953,   362.835), // = (   91 x 128  ) mm  = (  3.58 x 5.04  ) in
		'JIS_B8'                 => array(  181.417,   257.953), // = (   64 x 91   ) mm  = (  2.52 x 3.58  ) in
		'JIS_B9'                 => array(  127.559,   181.417), // = (   45 x 64   ) mm  = (  1.77 x 2.52  ) in
		'JIS_B10'                => array(   90.709,   127.559), // = (   32 x 45   ) mm  = (  1.26 x 1.77  ) in
		'JIS_B11'                => array(   62.362,    90.709), // = (   22 x 32   ) mm  = (  0.87 x 1.26  ) in
		'JIS_B12'                => array(   45.354,    62.362), // = (   16 x 22   ) mm  = (  0.63 x 0.87  ) in
		'PA0'                    => array( 2381.102,  3174.803), // = (  840 x 1120 ) mm  = ( 33.07 x 44.09 ) in
		'PA1'                    => array( 1587.402,  2381.102), // = (  560 x 840  ) mm  = ( 22.05 x 33.07 ) in
		'PA2'                    => array( 1190.551,  1587.402), // = (  420 x 560  ) mm  = ( 16.54 x 22.05 ) in
		'PA3'                    => array(  793.701,  1190.551), // = (  280 x 420  ) mm  = ( 11.02 x 16.54 ) in
		'PA4'                    => array(  595.276,   793.701), // = (  210 x 280  ) mm  = (  8.27 x 11.02 ) in
		'PA5'                    => array(  396.850,   595.276), // = (  140 x 210  ) mm  = (  5.51 x 8.27  ) in
		'PA6'                    => array(  297.638,   396.850), // = (  105 x 140  ) mm  = (  4.13 x 5.51  ) in
		'PA7'                    => array(  198.425,   297.638), // = (   70 x 105  ) mm  = (  2.76 x 4.13  ) in
		'PA8'                    => array(  147.402,   198.425), // = (   52 x 70   ) mm  = (  2.05 x 2.76  ) in
		'PA9'                    => array(   99.213,   147.402), // = (   35 x 52   ) mm  = (  1.38 x 2.05  ) in
		'PA10'                   => array(   73.701,    99.213), // = (   26 x 35   ) mm  = (  1.02 x 1.38  ) in
		'PASSPORT_PHOTO'         => array(   99.213,   127.559), // = (   35 x 45   ) mm  = (  1.38 x 1.77  ) in
		'E'                      => array(  233.858,   340.157), // = (   82 x 120  ) mm  = (  3.25 x 4.72  ) in
		'L'                      => array(  252.283,   360.000), // = (   89 x 127  ) mm  = (  3.50 x 5.00  ) in
		'3R'                     => array(  252.283,   360.000), // = (   89 x 127  ) mm  = (  3.50 x 5.00  ) in
		'KG'                     => array(  289.134,   430.866), // = (  102 x 152  ) mm  = (  4.02 x 5.98  ) in
		'4R'                     => array(  289.134,   430.866), // = (  102 x 152  ) mm  = (  4.02 x 5.98  ) in
		'4D'                     => array(  340.157,   430.866), // = (  120 x 152  ) mm  = (  4.72 x 5.98  ) in
		'2L'                     => array(  360.000,   504.567), // = (  127 x 178  ) mm  = (  5.00 x 7.01  ) in
		'5R'                     => array(  360.000,   504.567), // = (  127 x 178  ) mm  = (  5.00 x 7.01  ) in
		'8P'                     => array(  430.866,   575.433), // = (  152 x 203  ) mm  = (  5.98 x 7.99  ) in
		'6R'                     => array(  430.866,   575.433), // = (  152 x 203  ) mm  = (  5.98 x 7.99  ) in
		'6P'                     => array(  575.433,   720.000), // = (  203 x 254  ) mm  = (  7.99 x 10.00 ) in
		'8R'                     => array(  575.433,   720.000), // = (  203 x 254  ) mm  = (  7.99 x 10.00 ) in
		'6PW'                    => array(  575.433,   864.567), // = (  203 x 305  ) mm  = (  7.99 x 12.01 ) in
		'S8R'                    => array(  575.433,   864.567), // = (  203 x 305  ) mm  = (  7.99 x 12.01 ) in
		'4P'                     => array(  720.000,   864.567), // = (  254 x 305  ) mm  = ( 10.00 x 12.01 ) in
		'10R'                    => array(  720.000,   864.567), // = (  254 x 305  ) mm  = ( 10.00 x 12.01 ) in
		'4PW'                    => array(  720.000,  1080.000), // = (  254 x 381  ) mm  = ( 10.00 x 15.00 ) in
		'S10R'                   => array(  720.000,  1080.000), // = (  254 x 381  ) mm  = ( 10.00 x 15.00 ) in
		'11R'                    => array(  790.866,  1009.134), // = (  279 x 356  ) mm  = ( 10.98 x 14.02 ) in
		'S11R'                   => array(  790.866,  1224.567), // = (  279 x 432  ) mm  = ( 10.98 x 17.01 ) in
		'12R'                    => array(  864.567,  1080.000), // = (  305 x 381  ) mm  = ( 12.01 x 15.00 ) in
		'S12R'                   => array(  864.567,  1292.598), // = (  305 x 456  ) mm  = ( 12.01 x 17.95 ) in
		'NEWSPAPER_BROADSHEET'   => array( 2125.984,  1700.787), // = (  750 x 600  ) mm  = ( 29.53 x 23.62 ) in
		'NEWSPAPER_BERLINER'     => array( 1332.283,   892.913), // = (  470 x 315  ) mm  = ( 18.50 x 12.40 ) in
		'NEWSPAPER_TABLOID'      => array( 1218.898,   793.701), // = (  430 x 280  ) mm  = ( 16.93 x 11.02 ) in
		'NEWSPAPER_COMPACT'      => array( 1218.898,   793.701), // = (  430 x 280  ) mm  = ( 16.93 x 11.02 ) in
		'CREDIT_CARD'            => array(  153.014,   242.646), // = (   54 x 86   ) mm  = (  2.13 x 3.37  ) in
		'BUSINESS_CARD'          => array(  153.014,   242.646), // = (   54 x 86   ) mm  = (  2.13 x 3.37  ) in
		'BUSINESS_CARD_ISO7810'  => array(  153.014,   242.646), // = (   54 x 86   ) mm  = (  2.13 x 3.37  ) in
		'BUSINESS_CARD_ISO216'   => array(  147.402,   209.764), // = (   52 x 74   ) mm  = (  2.05 x 2.91  ) in
		'BUSINESS_CARD_IT'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
		'BUSINESS_CARD_UK'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
		'BUSINESS_CARD_FR'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
		'BUSINESS_CARD_DE'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
		'BUSINESS_CARD_ES'       => array(  155.906,   240.945), // = (   55 x 85   ) mm  = (  2.17 x 3.35  ) in
		'BUSINESS_CARD_CA'       => array(  144.567,   252.283), // = (   51 x 89   ) mm  = (  2.01 x 3.50  ) in
		'BUSINESS_CARD_US'       => array(  144.567,   252.283), // = (   51 x 89   ) mm  = (  2.01 x 3.50  ) in
		'BUSINESS_CARD_JP'       => array(  155.906,   257.953), // = (   55 x 91   ) mm  = (  2.17 x 3.58  ) in
		'BUSINESS_CARD_HK'       => array(  153.071,   255.118), // = (   54 x 90   ) mm  = (  2.13 x 3.54  ) in
		'BUSINESS_CARD_AU'       => array(  155.906,   255.118), // = (   55 x 90   ) mm  = (  2.17 x 3.54  ) in
		'BUSINESS_CARD_DK'       => array(  155.906,   255.118), // = (   55 x 90   ) mm  = (  2.17 x 3.54  ) in
		'BUSINESS_CARD_SE'       => array(  155.906,   255.118), // = (   55 x 90   ) mm  = (  2.17 x 3.54  ) in
		'BUSINESS_CARD_RU'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
		'BUSINESS_CARD_CZ'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
		'BUSINESS_CARD_FI'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
		'BUSINESS_CARD_HU'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
		'BUSINESS_CARD_IL'       => array(  141.732,   255.118), // = (   50 x 90   ) mm  = (  1.97 x 3.54  ) in
		'4SHEET'                 => array( 2880.000,  4320.000), // = ( 1016 x 1524 ) mm  = ( 40.00 x 60.00 ) in
		'6SHEET'                 => array( 3401.575,  5102.362), // = ( 1200 x 1800 ) mm  = ( 47.24 x 70.87 ) in
		'12SHEET'                => array( 8640.000,  4320.000), // = ( 3048 x 1524 ) mm  = (120.00 x 60.00 ) in
		'16SHEET'                => array( 5760.000,  8640.000), // = ( 2032 x 3048 ) mm  = ( 80.00 x 120.00) in
		'32SHEET'                => array(11520.000,  8640.000), // = ( 4064 x 3048 ) mm  = (160.00 x 120.00) in
		'48SHEET'                => array(17280.000,  8640.000), // = ( 6096 x 3048 ) mm  = (240.00 x 120.00) in
		'64SHEET'                => array(23040.000,  8640.000), // = ( 8128 x 3048 ) mm  = (320.00 x 120.00) in
		'96SHEET'                => array(34560.000,  8640.000), // = (12192 x 3048 ) mm  = (480.00 x 120.00) in
		'EN_EMPEROR'             => array( 3456.000,  5184.000), // = ( 1219 x 1829 ) mm  = ( 48.00 x 72.00 ) in
		'EN_ANTIQUARIAN'         => array( 2232.000,  3816.000), // = (  787 x 1346 ) mm  = ( 31.00 x 53.00 ) in
		'EN_GRAND_EAGLE'         => array( 2070.000,  3024.000), // = (  730 x 1067 ) mm  = ( 28.75 x 42.00 ) in
		'EN_DOUBLE_ELEPHANT'     => array( 1926.000,  2880.000), // = (  679 x 1016 ) mm  = ( 26.75 x 40.00 ) in
		'EN_ATLAS'               => array( 1872.000,  2448.000), // = (  660 x 864  ) mm  = ( 26.00 x 34.00 ) in
		'EN_COLOMBIER'           => array( 1692.000,  2484.000), // = (  597 x 876  ) mm  = ( 23.50 x 34.50 ) in
		'EN_ELEPHANT'            => array( 1656.000,  2016.000), // = (  584 x 711  ) mm  = ( 23.00 x 28.00 ) in
		'EN_DOUBLE_DEMY'         => array( 1620.000,  2556.000), // = (  572 x 902  ) mm  = ( 22.50 x 35.50 ) in
		'EN_IMPERIAL'            => array( 1584.000,  2160.000), // = (  559 x 762  ) mm  = ( 22.00 x 30.00 ) in
		'EN_PRINCESS'            => array( 1548.000,  2016.000), // = (  546 x 711  ) mm  = ( 21.50 x 28.00 ) in
		'EN_CARTRIDGE'           => array( 1512.000,  1872.000), // = (  533 x 660  ) mm  = ( 21.00 x 26.00 ) in
		'EN_DOUBLE_LARGE_POST'   => array( 1512.000,  2376.000), // = (  533 x 838  ) mm  = ( 21.00 x 33.00 ) in
		'EN_ROYAL'               => array( 1440.000,  1800.000), // = (  508 x 635  ) mm  = ( 20.00 x 25.00 ) in
		'EN_SHEET'               => array( 1404.000,  1692.000), // = (  495 x 597  ) mm  = ( 19.50 x 23.50 ) in
		'EN_HALF_POST'           => array( 1404.000,  1692.000), // = (  495 x 597  ) mm  = ( 19.50 x 23.50 ) in
		'EN_SUPER_ROYAL'         => array( 1368.000,  1944.000), // = (  483 x 686  ) mm  = ( 19.00 x 27.00 ) in
		'EN_DOUBLE_POST'         => array( 1368.000,  2196.000), // = (  483 x 775  ) mm  = ( 19.00 x 30.50 ) in
		'EN_MEDIUM'              => array( 1260.000,  1656.000), // = (  445 x 584  ) mm  = ( 17.50 x 23.00 ) in
		'EN_DEMY'                => array( 1260.000,  1620.000), // = (  445 x 572  ) mm  = ( 17.50 x 22.50 ) in
		'EN_LARGE_POST'          => array( 1188.000,  1512.000), // = (  419 x 533  ) mm  = ( 16.50 x 21.00 ) in
		'EN_COPY_DRAUGHT'        => array( 1152.000,  1440.000), // = (  406 x 508  ) mm  = ( 16.00 x 20.00 ) in
		'EN_POST'                => array( 1116.000,  1386.000), // = (  394 x 489  ) mm  = ( 15.50 x 19.25 ) in
		'EN_CROWN'               => array( 1080.000,  1440.000), // = (  381 x 508  ) mm  = ( 15.00 x 20.00 ) in
		'EN_PINCHED_POST'        => array( 1062.000,  1332.000), // = (  375 x 470  ) mm  = ( 14.75 x 18.50 ) in
		'EN_BRIEF'               => array(  972.000,  1152.000), // = (  343 x 406  ) mm  = ( 13.50 x 16.00 ) in
		'EN_FOOLSCAP'            => array(  972.000,  1224.000), // = (  343 x 432  ) mm  = ( 13.50 x 17.00 ) in
		'EN_SMALL_FOOLSCAP'      => array(  954.000,  1188.000), // = (  337 x 419  ) mm  = ( 13.25 x 16.50 ) in
		'EN_POTT'                => array(  900.000,  1080.000), // = (  318 x 381  ) mm  = ( 12.50 x 15.00 ) in
		'BE_GRAND_AIGLE'         => array( 1984.252,  2948.031), // = (  700 x 1040 ) mm  = ( 27.56 x 40.94 ) in
		'BE_COLOMBIER'           => array( 1757.480,  2409.449), // = (  620 x 850  ) mm  = ( 24.41 x 33.46 ) in
		'BE_DOUBLE_CARRE'        => array( 1757.480,  2607.874), // = (  620 x 920  ) mm  = ( 24.41 x 36.22 ) in
		'BE_ELEPHANT'            => array( 1746.142,  2182.677), // = (  616 x 770  ) mm  = ( 24.25 x 30.31 ) in
		'BE_PETIT_AIGLE'         => array( 1700.787,  2381.102), // = (  600 x 840  ) mm  = ( 23.62 x 33.07 ) in
		'BE_GRAND_JESUS'         => array( 1559.055,  2069.291), // = (  550 x 730  ) mm  = ( 21.65 x 28.74 ) in
		'BE_JESUS'               => array( 1530.709,  2069.291), // = (  540 x 730  ) mm  = ( 21.26 x 28.74 ) in
		'BE_RAISIN'              => array( 1417.323,  1842.520), // = (  500 x 650  ) mm  = ( 19.69 x 25.59 ) in
		'BE_GRAND_MEDIAN'        => array( 1303.937,  1714.961), // = (  460 x 605  ) mm  = ( 18.11 x 23.82 ) in
		'BE_DOUBLE_POSTE'        => array( 1233.071,  1601.575), // = (  435 x 565  ) mm  = ( 17.13 x 22.24 ) in
		'BE_COQUILLE'            => array( 1218.898,  1587.402), // = (  430 x 560  ) mm  = ( 16.93 x 22.05 ) in
		'BE_PETIT_MEDIAN'        => array( 1176.378,  1502.362), // = (  415 x 530  ) mm  = ( 16.34 x 20.87 ) in
		'BE_RUCHE'               => array( 1020.472,  1303.937), // = (  360 x 460  ) mm  = ( 14.17 x 18.11 ) in
		'BE_PROPATRIA'           => array(  977.953,  1218.898), // = (  345 x 430  ) mm  = ( 13.58 x 16.93 ) in
		'BE_LYS'                 => array(  898.583,  1125.354), // = (  317 x 397  ) mm  = ( 12.48 x 15.63 ) in
		'BE_POT'                 => array(  870.236,  1088.504), // = (  307 x 384  ) mm  = ( 12.09 x 15.12 ) in
		'BE_ROSETTE'             => array(  765.354,   983.622), // = (  270 x 347  ) mm  = ( 10.63 x 13.66 ) in
		'FR_UNIVERS'             => array( 2834.646,  3685.039), // = ( 1000 x 1300 ) mm  = ( 39.37 x 51.18 ) in
		'FR_DOUBLE_COLOMBIER'    => array( 2551.181,  3571.654), // = (  900 x 1260 ) mm  = ( 35.43 x 49.61 ) in
		'FR_GRANDE_MONDE'        => array( 2551.181,  3571.654), // = (  900 x 1260 ) mm  = ( 35.43 x 49.61 ) in
		'FR_DOUBLE_SOLEIL'       => array( 2267.717,  3401.575), // = (  800 x 1200 ) mm  = ( 31.50 x 47.24 ) in
		'FR_DOUBLE_JESUS'        => array( 2154.331,  3174.803), // = (  760 x 1120 ) mm  = ( 29.92 x 44.09 ) in
		'FR_GRAND_AIGLE'         => array( 2125.984,  3004.724), // = (  750 x 1060 ) mm  = ( 29.53 x 41.73 ) in
		'FR_PETIT_AIGLE'         => array( 1984.252,  2664.567), // = (  700 x 940  ) mm  = ( 27.56 x 37.01 ) in
		'FR_DOUBLE_RAISIN'       => array( 1842.520,  2834.646), // = (  650 x 1000 ) mm  = ( 25.59 x 39.37 ) in
		'FR_JOURNAL'             => array( 1842.520,  2664.567), // = (  650 x 940  ) mm  = ( 25.59 x 37.01 ) in
		'FR_COLOMBIER_AFFICHE'   => array( 1785.827,  2551.181), // = (  630 x 900  ) mm  = ( 24.80 x 35.43 ) in
		'FR_DOUBLE_CAVALIER'     => array( 1757.480,  2607.874), // = (  620 x 920  ) mm  = ( 24.41 x 36.22 ) in
		'FR_CLOCHE'              => array( 1700.787,  2267.717), // = (  600 x 800  ) mm  = ( 23.62 x 31.50 ) in
		'FR_SOLEIL'              => array( 1700.787,  2267.717), // = (  600 x 800  ) mm  = ( 23.62 x 31.50 ) in
		'FR_DOUBLE_CARRE'        => array( 1587.402,  2551.181), // = (  560 x 900  ) mm  = ( 22.05 x 35.43 ) in
		'FR_DOUBLE_COQUILLE'     => array( 1587.402,  2494.488), // = (  560 x 880  ) mm  = ( 22.05 x 34.65 ) in
		'FR_JESUS'               => array( 1587.402,  2154.331), // = (  560 x 760  ) mm  = ( 22.05 x 29.92 ) in
		'FR_RAISIN'              => array( 1417.323,  1842.520), // = (  500 x 650  ) mm  = ( 19.69 x 25.59 ) in
		'FR_CAVALIER'            => array( 1303.937,  1757.480), // = (  460 x 620  ) mm  = ( 18.11 x 24.41 ) in
		'FR_DOUBLE_COURONNE'     => array( 1303.937,  2040.945), // = (  460 x 720  ) mm  = ( 18.11 x 28.35 ) in
		'FR_CARRE'               => array( 1275.591,  1587.402), // = (  450 x 560  ) mm  = ( 17.72 x 22.05 ) in
		'FR_COQUILLE'            => array( 1247.244,  1587.402), // = (  440 x 560  ) mm  = ( 17.32 x 22.05 ) in
		'FR_DOUBLE_TELLIERE'     => array( 1247.244,  1927.559), // = (  440 x 680  ) mm  = ( 17.32 x 26.77 ) in
		'FR_DOUBLE_CLOCHE'       => array( 1133.858,  1700.787), // = (  400 x 600  ) mm  = ( 15.75 x 23.62 ) in
		'FR_DOUBLE_POT'          => array( 1133.858,  1757.480), // = (  400 x 620  ) mm  = ( 15.75 x 24.41 ) in
		'FR_ECU'                 => array( 1133.858,  1474.016), // = (  400 x 520  ) mm  = ( 15.75 x 20.47 ) in
		'FR_COURONNE'            => array( 1020.472,  1303.937), // = (  360 x 460  ) mm  = ( 14.17 x 18.11 ) in
		'FR_TELLIERE'            => array(  963.780,  1247.244), // = (  340 x 440  ) mm  = ( 13.39 x 17.32 ) in
		'FR_POT'                 => array(  878.740,  1133.858), // = (  310 x 400  ) mm  = ( 12.20 x 15.75 ) in
	);
	public static function getTCPDFVersion() {
		return self::$tcpdf_version;
	}
	public static function getTCPDFProducer() {
		return "\x54\x43\x50\x44\x46\x20".self::getTCPDFVersion()."\x20\x28\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x74\x63\x70\x64\x66\x2e\x6f\x72\x67\x29";
	}
	public static function set_mqr($mqr) {
		if (!defined('PHP_VERSION_ID')) {
			$version = PHP_VERSION;
			define('PHP_VERSION_ID', (($version[0] * 10000) + ($version[2] * 100) + $version[4]));
		}
		if (PHP_VERSION_ID < 50300) {
			@set_magic_quotes_runtime($mqr);
		}
	}
	public static function get_mqr() {
		if (!defined('PHP_VERSION_ID')) {
			$version = PHP_VERSION;
			define('PHP_VERSION_ID', (($version[0] * 10000) + ($version[2] * 100) + $version[4]));
		}
		if (PHP_VERSION_ID < 50300) {
			return @get_magic_quotes_runtime();
		}
		return 0;
	}
	public static function getPageSizeFromFormat($format) {
		if (isset(self::$page_formats[$format])) {
			return self::$page_formats[$format];
		}
		return self::$page_formats['A4'];
	}
	public static function setPageBoxes($page, $type, $llx, $lly, $urx, $ury, $points=false, $k, $pagedim=array()) {
		if (!isset($pagedim[$page])) {
			$pagedim[$page] = array();
		}
		if (!in_array($type, self::$pageboxes)) {
			return;
		}
		if ($points) {
			$k = 1;
		}
		$pagedim[$page][$type]['llx'] = ($llx * $k);
		$pagedim[$page][$type]['lly'] = ($lly * $k);
		$pagedim[$page][$type]['urx'] = ($urx * $k);
		$pagedim[$page][$type]['ury'] = ($ury * $k);
		return $pagedim;
	}
	public static function swapPageBoxCoordinates($page, $pagedim) {
		foreach (self::$pageboxes as $type) {
			if (isset($pagedim[$page][$type])) {
				$tmp = $pagedim[$page][$type]['llx'];
				$pagedim[$page][$type]['llx'] = $pagedim[$page][$type]['lly'];
				$pagedim[$page][$type]['lly'] = $tmp;
				$tmp = $pagedim[$page][$type]['urx'];
				$pagedim[$page][$type]['urx'] = $pagedim[$page][$type]['ury'];
				$pagedim[$page][$type]['ury'] = $tmp;
			}
		}
		return $pagedim;
	}
	public static function getPageLayoutMode($layout='SinglePage') {
		switch ($layout) {
			case 'default':
			case 'single':
			case 'SinglePage': {
				$layout_mode = 'SinglePage';
				break;
			}
			case 'continuous':
			case 'OneColumn': {
				$layout_mode = 'OneColumn';
				break;
			}
			case 'two':
			case 'TwoColumnLeft': {
				$layout_mode = 'TwoColumnLeft';
				break;
			}
			case 'TwoColumnRight': {
				$layout_mode = 'TwoColumnRight';
				break;
			}
			case 'TwoPageLeft': {
				$layout_mode = 'TwoPageLeft';
				break;
			}
			case 'TwoPageRight': {
				$layout_mode = 'TwoPageRight';
				break;
			}
			default: {
				$layout_mode = 'SinglePage';
			}
		}
		return $layout_mode;
	}
	public static function getPageMode($mode='UseNone') {
		switch ($mode) {
			case 'UseNone': {
				$page_mode = 'UseNone';
				break;
			}
			case 'UseOutlines': {
				$page_mode = 'UseOutlines';
				break;
			}
			case 'UseThumbs': {
				$page_mode = 'UseThumbs';
				break;
			}
			case 'FullScreen': {
				$page_mode = 'FullScreen';
				break;
			}
			case 'UseOC': {
				$page_mode = 'UseOC';
				break;
			}
			case '': {
				$page_mode = 'UseAttachments';
				break;
			}
			default: {
				$page_mode = 'UseNone';
			}
		}
		return $page_mode;
	}
	public static function isValidURL($url) {
		$headers = @get_headers($url);
    	return (strpos($headers[0], '200') !== false);
	}
	public static function removeSHY($txt='', $unicode=true) {
		$txt = preg_replace('/([\\xc2]{1}[\\xad]{1})/', '', $txt);
		if (!$unicode) {
			$txt = preg_replace('/([\\xad]{1})/', '', $txt);
		}
		return $txt;
	}
	public static function getBorderMode($brd, $position='start', $opencell=true) {
		if ((!$opencell) OR empty($brd)) {
			return $brd;
		}
		if ($brd == 1) {
			$brd = 'LTRB';
		}
		if (is_string($brd)) {
			$slen = strlen($brd);
			$newbrd = array();
			for ($i = 0; $i < $slen; ++$i) {
				$newbrd[$brd[$i]] = array('cap' => 'square', 'join' => 'miter');
			}
			$brd = $newbrd;
		}
		foreach ($brd as $border => $style) {
			switch ($position) {
				case 'start': {
					if (strpos($border, 'B') !== false) {
						$newkey = str_replace('B', '', $border);
						if (strlen($newkey) > 0) {
							$brd[$newkey] = $style;
						}
						unset($brd[$border]);
					}
					break;
				}
				case 'middle': {
					if (strpos($border, 'B') !== false) {
						$newkey = str_replace('B', '', $border);
						if (strlen($newkey) > 0) {
							$brd[$newkey] = $style;
						}
						unset($brd[$border]);
						$border = $newkey;
					}
					if (strpos($border, 'T') !== false) {
						$newkey = str_replace('T', '', $border);
						if (strlen($newkey) > 0) {
							$brd[$newkey] = $style;
						}
						unset($brd[$border]);
					}
					break;
				}
				case 'end': {
					if (strpos($border, 'T') !== false) {
						$newkey = str_replace('T', '', $border);
						if (strlen($newkey) > 0) {
							$brd[$newkey] = $style;
						}
						unset($brd[$border]);
					}
					break;
				}
			}
		}
		return $brd;
	}
	public static function empty_string($str) {
		return (is_null($str) OR (is_string($str) AND (strlen($str) == 0)));
	}
	public static function getObjFilename($type='tmp', $file_id='') {
		return tempnam(K_PATH_CACHE, '__tcpdf_'.$file_id.'_'.$type.'_'.md5(TCPDF_STATIC::getRandomSeed()).'_');
	}
	public static function _escape($s) {
		return strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r'));
	}
	public static function _escapeXML($str) {
		$replaceTable = array("\0" => '', '&' => '&amp;', '<' => '&lt;', '>' => '&gt;');
		$str = strtr($str, $replaceTable);
		return $str;
	}
	public static function objclone($object) {
		if (($object instanceof Imagick) AND (version_compare(phpversion('imagick'), '3.0.1') !== 1)) {
			return @$object->clone();
		}
		return @clone($object);
	}
	public static function sendOutputData($data, $length) {
		if (!isset($_SERVER['HTTP_ACCEPT_ENCODING']) OR empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			header('Content-Length: '.$length);
		}
		echo $data;
	}
	public static function replacePageNumAliases($page, $replace, $diff=0) {
		foreach ($replace as $rep) {
			foreach ($rep[3] as $a) {
				if (strpos($page, $a) !== false) {
					$page = str_replace($a, $rep[0], $page);
					$diff += ($rep[2] - $rep[1]);
				}
			}
		}
		return array($page, $diff);
	}
	public static function getTimestamp($date) {
		if (($date[0] == 'D') AND ($date[1] == ':')) {
			$date = substr($date, 2);
		}
		return strtotime($date);
	}
	public static function getFormattedDate($time) {
		return substr_replace(date('YmdHisO', intval($time)), '\'', (0 - 2), 0).'\'';
	}
	public static function _getULONG($str, $offset) {
		$v = unpack('Ni', substr($str, $offset, 4));
		return $v['i'];
	}
	public static function _getUSHORT($str, $offset) {
		$v = unpack('ni', substr($str, $offset, 2));
		return $v['i'];
	}
	public static function _getSHORT($str, $offset) {
		$v = unpack('si', substr($str, $offset, 2));
		return $v['i'];
	}
	public static function _getFWORD($str, $offset) {
		$v = self::_getUSHORT($str, $offset);
		if ($v > 0x7fff) {
			$v -= 0x10000;
		}
		return $v;
	}
	public static function _getUFWORD($str, $offset) {
		$v = self::_getUSHORT($str, $offset);
		return $v;
	}
	public static function _getFIXED($str, $offset) {
		$m = self::_getFWORD($str, $offset);
		$f = self::_getUSHORT($str, ($offset + 2));
		$v = floatval(''.$m.'.'.$f.'');
		return $v;
	}
	public static function _getBYTE($str, $offset) {
		$v = unpack('Ci', substr($str, $offset, 1));
		return $v['i'];
	}
	public static function rfread($handle, $length) {
		$data = fread($handle, $length);
		if ($data === false) {
			return false;
		}
		$rest = ($length - strlen($data));
		if (($rest > 0) && !feof($handle)) {
			$data .= self::rfread($handle, $rest);
		}
		return $data;
	}
	public static function _freadint($f) {
		$a = unpack('Ni', fread($f, 4));
		return $a['i'];
	}
	public static function getRandomSeed($seed='') {
		$rnd = uniqid(rand().microtime(true), true);
		if (function_exists('posix_getpid')) {
			$rnd .= posix_getpid();
		}
		if (function_exists('openssl_random_pseudo_bytes') AND (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')) {
			$rnd .= openssl_random_pseudo_bytes(512);
		} else {
			for ($i = 0; $i < 23; ++$i) {
				$rnd .= uniqid('', true);
			}
		}
		return $rnd.$seed.__FILE__.serialize($_SERVER).microtime(true);
	}
	public static function _md5_16($str) {
		return pack('H*', md5($str));
	}
	public static function _AES($key, $text) {
		$padding = 16 - (strlen($text) % 16);
		$text .= str_repeat(chr($padding), $padding);
		if (extension_loaded('openssl')) {
			$iv = openssl_random_pseudo_bytes (openssl_cipher_iv_length('aes-256-cbc'));
			$text = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
			return $iv.substr($text, 0, -16);
		}
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
		$text = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC, $iv);
		$text = $iv.$text;
		return $text;
	}
	public static function _AESnopad($key, $text) {
		if (extension_loaded('openssl')) {
			$iv = str_repeat("\x00", openssl_cipher_iv_length('aes-256-cbc'));
			$text = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
			return substr($text, 0, -16);
		}
		$iv = str_repeat("\x00", mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
		$text = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC, $iv);
		return $text;
	}
	public static function _RC4($key, $text, &$last_enc_key, &$last_enc_key_c) {
		if (function_exists('mcrypt_encrypt') AND ($out = @mcrypt_encrypt(MCRYPT_ARCFOUR, $key, $text, MCRYPT_MODE_STREAM, ''))) {
			return $out;
		}
		if ($last_enc_key != $key) {
			$k = str_repeat($key, ((256 / strlen($key)) + 1));
			$rc4 = range(0, 255);
			$j = 0;
			for ($i = 0; $i < 256; ++$i) {
				$t = $rc4[$i];
				$j = ($j + $t + ord($k[$i])) % 256;
				$rc4[$i] = $rc4[$j];
				$rc4[$j] = $t;
			}
			$last_enc_key = $key;
			$last_enc_key_c = $rc4;
		} else {
			$rc4 = $last_enc_key_c;
		}
		$len = strlen($text);
		$a = 0;
		$b = 0;
		$out = '';
		for ($i = 0; $i < $len; ++$i) {
			$a = ($a + 1) % 256;
			$t = $rc4[$a];
			$b = ($b + $t) % 256;
			$rc4[$a] = $rc4[$b];
			$rc4[$b] = $t;
			$k = $rc4[($rc4[$a] + $rc4[$b]) % 256];
			$out .= chr(ord($text[$i]) ^ $k);
		}
		return $out;
	}
	public static function getUserPermissionCode($permissions, $mode=0) {
		$options = array(
			'owner' => 2, // bit 2 -- inverted logic: cleared by default
			'print' => 4, // bit 3
			'modify' => 8, // bit 4
			'copy' => 16, // bit 5
			'annot-forms' => 32, // bit 6
			'fill-forms' => 256, // bit 9
			'extract' => 512, // bit 10
			'assemble' => 1024,// bit 11
			'print-high' => 2048 // bit 12
			);
		$protection = 2147422012; // 32 bit: (01111111 11111111 00001111 00111100)
		foreach ($permissions as $permission) {
			if (isset($options[$permission])) {
				if (($mode > 0) OR ($options[$permission] <= 32)) {
					if ($options[$permission] == 2) {
						$protection += $options[$permission];
					} else {
						$protection -= $options[$permission];
					}
				}
			}
		}
		return $protection;
	}
	public static function convertHexStringToString($bs) {
		$string = ''; // string to be returned
		$bslength = strlen($bs);
		if (($bslength % 2) != 0) {
			$bs .= '0';
			++$bslength;
		}
		for ($i = 0; $i < $bslength; $i += 2) {
			$string .= chr(hexdec($bs[$i].$bs[($i + 1)]));
		}
		return $string;
	}
	public static function convertStringToHexString($s) {
		$bs = '';
		$chars = preg_split('//', $s, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($chars as $c) {
			$bs .= sprintf('%02s', dechex(ord($c)));
		}
		return $bs;
	}
	public static function getEncPermissionsString($protection) {
		$binprot = sprintf('%032b', $protection);
		$str = chr(bindec(substr($binprot, 24, 8)));
		$str .= chr(bindec(substr($binprot, 16, 8)));
		$str .= chr(bindec(substr($binprot, 8, 8)));
		$str .= chr(bindec(substr($binprot, 0, 8)));
		return $str;
	}
	public static function encodeNameObject($name) {
		$escname = '';
		$length = strlen($name);
		for ($i = 0; $i < $length; ++$i) {
			$chr = $name[$i];
			if (preg_match('/[0-9a-zA-Z#_=-]/', $chr) == 1) {
				$escname .= $chr;
			} else {
				$escname .= sprintf('#%02X', ord($chr));
			}
		}
		return $escname;
	}
	public static function getAnnotOptFromJSProp($prop, &$spot_colors, $rtl=false) {
		if (isset($prop['aopt']) AND is_array($prop['aopt'])) {
			return $prop['aopt'];
		}
		$opt = array(); // value to be returned
		if (isset($prop['alignment'])) {
			switch ($prop['alignment']) {
				case 'left': {
					$opt['q'] = 0;
					break;
				}
				case 'center': {
					$opt['q'] = 1;
					break;
				}
				case 'right': {
					$opt['q'] = 2;
					break;
				}
				default: {
					$opt['q'] = ($rtl)?2:0;
					break;
				}
			}
		}
		if (isset($prop['lineWidth'])) {
			$linewidth = intval($prop['lineWidth']);
		} else {
			$linewidth = 1;
		}
		if (isset($prop['borderStyle'])) {
			switch ($prop['borderStyle']) {
				case 'border.d':
				case 'dashed': {
					$opt['border'] = array(0, 0, $linewidth, array(3, 2));
					$opt['bs'] = array('w'=>$linewidth, 's'=>'D', 'd'=>array(3, 2));
					break;
				}
				case 'border.b':
				case 'beveled': {
					$opt['border'] = array(0, 0, $linewidth);
					$opt['bs'] = array('w'=>$linewidth, 's'=>'B');
					break;
				}
				case 'border.i':
				case 'inset': {
					$opt['border'] = array(0, 0, $linewidth);
					$opt['bs'] = array('w'=>$linewidth, 's'=>'I');
					break;
				}
				case 'border.u':
				case 'underline': {
					$opt['border'] = array(0, 0, $linewidth);
					$opt['bs'] = array('w'=>$linewidth, 's'=>'U');
					break;
				}
				case 'border.s':
				case 'solid': {
					$opt['border'] = array(0, 0, $linewidth);
					$opt['bs'] = array('w'=>$linewidth, 's'=>'S');
					break;
				}
				default: {
					break;
				}
			}
		}
		if (isset($prop['border']) AND is_array($prop['border'])) {
			$opt['border'] = $prop['border'];
		}
		if (!isset($opt['mk'])) {
			$opt['mk'] = array();
		}
		if (!isset($opt['mk']['if'])) {
			$opt['mk']['if'] = array();
		}
		$opt['mk']['if']['a'] = array(0.5, 0.5);
		if (isset($prop['buttonAlignX'])) {
			$opt['mk']['if']['a'][0] = $prop['buttonAlignX'];
		}
		if (isset($prop['buttonAlignY'])) {
			$opt['mk']['if']['a'][1] = $prop['buttonAlignY'];
		}
		if (isset($prop['buttonFitBounds']) AND ($prop['buttonFitBounds'] == 'true')) {
			$opt['mk']['if']['fb'] = true;
		}
		if (isset($prop['buttonScaleHow'])) {
			switch ($prop['buttonScaleHow']) {
				case 'scaleHow.proportional': {
					$opt['mk']['if']['s'] = 'P';
					break;
				}
				case 'scaleHow.anamorphic': {
					$opt['mk']['if']['s'] = 'A';
					break;
				}
			}
		}
		if (isset($prop['buttonScaleWhen'])) {
			switch ($prop['buttonScaleWhen']) {
				case 'scaleWhen.always': {
					$opt['mk']['if']['sw'] = 'A';
					break;
				}
				case 'scaleWhen.never': {
					$opt['mk']['if']['sw'] = 'N';
					break;
				}
				case 'scaleWhen.tooBig': {
					$opt['mk']['if']['sw'] = 'B';
					break;
				}
				case 'scaleWhen.tooSmall': {
					$opt['mk']['if']['sw'] = 'S';
					break;
				}
			}
		}
		if (isset($prop['buttonPosition'])) {
			switch ($prop['buttonPosition']) {
				case 0:
				case 'position.textOnly': {
					$opt['mk']['tp'] = 0;
					break;
				}
				case 1:
				case 'position.iconOnly': {
					$opt['mk']['tp'] = 1;
					break;
				}
				case 2:
				case 'position.iconTextV': {
					$opt['mk']['tp'] = 2;
					break;
				}
				case 3:
				case 'position.textIconV': {
					$opt['mk']['tp'] = 3;
					break;
				}
				case 4:
				case 'position.iconTextH': {
					$opt['mk']['tp'] = 4;
					break;
				}
				case 5:
				case 'position.textIconH': {
					$opt['mk']['tp'] = 5;
					break;
				}
				case 6:
				case 'position.overlay': {
					$opt['mk']['tp'] = 6;
					break;
				}
			}
		}
		if (isset($prop['fillColor'])) {
			if (is_array($prop['fillColor'])) {
				$opt['mk']['bg'] = $prop['fillColor'];
			} else {
				$opt['mk']['bg'] = TCPDF_COLORS::convertHTMLColorToDec($prop['fillColor'], $spot_colors);
			}
		}
		if (isset($prop['strokeColor'])) {
			if (is_array($prop['strokeColor'])) {
				$opt['mk']['bc'] = $prop['strokeColor'];
			} else {
				$opt['mk']['bc'] = TCPDF_COLORS::convertHTMLColorToDec($prop['strokeColor'], $spot_colors);
			}
		}
		if (isset($prop['rotation'])) {
			$opt['mk']['r'] = $prop['rotation'];
		}
		if (isset($prop['charLimit'])) {
			$opt['maxlen'] = intval($prop['charLimit']);
		}
		if (!isset($ff)) {
			$ff = 0; // default value
		}
		if (isset($prop['readonly']) AND ($prop['readonly'] == 'true')) {
			$ff += 1 << 0;
		}
		if (isset($prop['required']) AND ($prop['required'] == 'true')) {
			$ff += 1 << 1;
		}
		if (isset($prop['multiline']) AND ($prop['multiline'] == 'true')) {
			$ff += 1 << 12;
		}
		if (isset($prop['password']) AND ($prop['password'] == 'true')) {
			$ff += 1 << 13;
		}
		if (isset($prop['NoToggleToOff']) AND ($prop['NoToggleToOff'] == 'true')) {
			$ff += 1 << 14;
		}
		if (isset($prop['Radio']) AND ($prop['Radio'] == 'true')) {
			$ff += 1 << 15;
		}
		if (isset($prop['Pushbutton']) AND ($prop['Pushbutton'] == 'true')) {
			$ff += 1 << 16;
		}
		if (isset($prop['Combo']) AND ($prop['Combo'] == 'true')) {
			$ff += 1 << 17;
		}
		if (isset($prop['editable']) AND ($prop['editable'] == 'true')) {
			$ff += 1 << 18;
		}
		if (isset($prop['Sort']) AND ($prop['Sort'] == 'true')) {
			$ff += 1 << 19;
		}
		if (isset($prop['fileSelect']) AND ($prop['fileSelect'] == 'true')) {
			$ff += 1 << 20;
		}
		if (isset($prop['multipleSelection']) AND ($prop['multipleSelection'] == 'true')) {
			$ff += 1 << 21;
		}
		if (isset($prop['doNotSpellCheck']) AND ($prop['doNotSpellCheck'] == 'true')) {
			$ff += 1 << 22;
		}
		if (isset($prop['doNotScroll']) AND ($prop['doNotScroll'] == 'true')) {
			$ff += 1 << 23;
		}
		if (isset($prop['comb']) AND ($prop['comb'] == 'true')) {
			$ff += 1 << 24;
		}
		if (isset($prop['radiosInUnison']) AND ($prop['radiosInUnison'] == 'true')) {
			$ff += 1 << 25;
		}
		if (isset($prop['richText']) AND ($prop['richText'] == 'true')) {
			$ff += 1 << 25;
		}
		if (isset($prop['commitOnSelChange']) AND ($prop['commitOnSelChange'] == 'true')) {
			$ff += 1 << 26;
		}
		$opt['ff'] = $ff;
		if (isset($prop['defaultValue'])) {
			$opt['dv'] = $prop['defaultValue'];
		}
		$f = 4; // default value for annotation flags
		if (isset($prop['readonly']) AND ($prop['readonly'] == 'true')) {
			$f += 1 << 6;
		}
		if (isset($prop['display'])) {
			if ($prop['display'] == 'display.visible') {
				//
			} elseif ($prop['display'] == 'display.hidden') {
				$f += 1 << 1;
			} elseif ($prop['display'] == 'display.noPrint') {
				$f -= 1 << 2;
			} elseif ($prop['display'] == 'display.noView') {
				$f += 1 << 5;
			}
		}
		$opt['f'] = $f;
		if (isset($prop['currentValueIndices']) AND is_array($prop['currentValueIndices'])) {
			$opt['i'] = $prop['currentValueIndices'];
		}
		if (isset($prop['value'])) {
			if (is_array($prop['value'])) {
				$opt['opt'] = array();
				foreach ($prop['value'] AS $key => $optval) {
					if (isset($prop['exportValues'][$key])) {
						$opt['opt'][$key] = array($prop['exportValues'][$key], $prop['value'][$key]);
					} else {
						$opt['opt'][$key] = $prop['value'][$key];
					}
				}
			} else {
				$opt['v'] = $prop['value'];
			}
		}
		if (isset($prop['richValue'])) {
			$opt['rv'] = $prop['richValue'];
		}
		if (isset($prop['submitName'])) {
			$opt['tm'] = $prop['submitName'];
		}
		if (isset($prop['name'])) {
			$opt['t'] = $prop['name'];
		}
		if (isset($prop['userName'])) {
			$opt['tu'] = $prop['userName'];
		}
		if (isset($prop['highlight'])) {
			switch ($prop['highlight']) {
				case 'none':
				case 'highlight.n': {
					$opt['h'] = 'N';
					break;
				}
				case 'invert':
				case 'highlight.i': {
					$opt['h'] = 'i';
					break;
				}
				case 'push':
				case 'highlight.p': {
					$opt['h'] = 'P';
					break;
				}
				case 'outline':
				case 'highlight.o': {
					$opt['h'] = 'O';
					break;
				}
			}
		}
		return $opt;
	}
	public static function formatPageNumber($num) {
		return number_format((float)$num, 0, '', '.');
	}
	public static function formatTOCPageNumber($num) {
		return number_format((float)$num, 0, '', '.');
	}
	public static function extractCSSproperties($cssdata) {
		if (empty($cssdata)) {
			return array();
		}
		$cssdata = preg_replace('/\/\*[^\*]*\*\//', '', $cssdata);
		$cssdata = preg_replace('/[\s]+/', ' ', $cssdata);
		$cssdata = preg_replace('/[\s]*([;:\{\}]{1})[\s]*/', '\\1', $cssdata);
		$cssdata = preg_replace('/([^\}\{]+)\{\}/', '', $cssdata);
		$cssdata = preg_replace('/@media[\s]+([^\{]*)\{/i', '@media \\1§', $cssdata);
		$cssdata = preg_replace('/\}\}/si', '}§', $cssdata);
		$cssdata = trim($cssdata);
		$cssblocks = array();
		$matches = array();
		if (preg_match_all('/@media[\s]+([^\§]*)§([^§]*)§/i', $cssdata, $matches) > 0) {
			foreach ($matches[1] as $key => $type) {
				$cssblocks[$type] = $matches[2][$key];
			}
			$cssdata = preg_replace('/@media[\s]+([^\§]*)§([^§]*)§/i', '', $cssdata);
		}
		if (isset($cssblocks['all']) AND !empty($cssblocks['all'])) {
			$cssdata .= $cssblocks['all'];
		}
		if (isset($cssblocks['print']) AND !empty($cssblocks['print'])) {
			$cssdata .= $cssblocks['print'];
		}
		$cssblocks = array();
		$matches = array();
		if (substr($cssdata, -1) == '}') {
			$cssdata = substr($cssdata, 0, -1);
		}
		$matches = explode('}', $cssdata);
		foreach ($matches as $key => $block) {
			$cssblocks[$key] = explode('{', $block);
			if (!isset($cssblocks[$key][1])) {
				unset($cssblocks[$key]);
			}
		}
		foreach ($cssblocks as $key => $block) {
			if (strpos($block[0], ',') > 0) {
				$selectors = explode(',', $block[0]);
				foreach ($selectors as $sel) {
					$cssblocks[] = array(0 => trim($sel), 1 => $block[1]);
				}
				unset($cssblocks[$key]);
			}
		}
		$cssdata = array();
		foreach ($cssblocks as $block) {
			$selector = $block[0];
			$matches = array();
			$a = 0; // the declaration is not from is a 'style' attribute
			$b = intval(preg_match_all('/[\#]/', $selector, $matches)); // number of ID attributes
			$c = intval(preg_match_all('/[\[\.]/', $selector, $matches)); // number of other attributes
			$c += intval(preg_match_all('/[\:]link|visited|hover|active|focus|target|lang|enabled|disabled|checked|indeterminate|root|nth|first|last|only|empty|contains|not/i', $selector, $matches)); // number of pseudo-classes
			$d = intval(preg_match_all('/[\>\+\~\s]{1}[a-zA-Z0-9]+/', ' '.$selector, $matches)); // number of element names
			$d += intval(preg_match_all('/[\:][\:]/', $selector, $matches)); // number of pseudo-elements
			$specificity = $a.$b.$c.$d;
			$cssdata[$specificity.' '.$selector] = $block[1];
		}
		ksort($cssdata, SORT_STRING);
		return $cssdata;
	}
	public static function fixHTMLCode($html, $default_css='', $tagvs='', $tidy_options='', &$tagvspaces) {
		if ($tidy_options === '') {
			$tidy_options = array (
				'clean' => 1,
				'drop-empty-paras' => 0,
				'drop-proprietary-attributes' => 1,
				'fix-backslash' => 1,
				'hide-comments' => 1,
				'join-styles' => 1,
				'lower-literals' => 1,
				'merge-divs' => 1,
				'merge-spans' => 1,
				'output-xhtml' => 1,
				'word-2000' => 1,
				'wrap' => 0,
				'output-bom' => 0,
			);
		}
		$tidy = tidy_parse_string($html, $tidy_options);
		$tidy->cleanRepair();
		$tidy_head = tidy_get_head($tidy);
		$css = $tidy_head->value;
		$css = preg_replace('/<style([^>]+)>/ims', '<style>', $css);
		$css = preg_replace('/<\/style>(.*)<style>/ims', "\n", $css);
		$css = str_replace('', '', $css);
		$css = str_replace('', '', $css);
		preg_match('/<style>(.*)<\/style>/ims', $css, $matches);
		if (isset($matches[1])) {
			$css = strtolower($matches[1]);
		} else {
			$css = '';
		}
		$css = '<style>'.$default_css.$css.'</style>';
		$tidy_body = tidy_get_body($tidy);
		$html = $tidy_body->value;
		$html = str_replace('<br>', '<br />', $html);
		$html = preg_replace('/<div([^\>]*)><\/div>/', '', $html);
		$html = preg_replace('/<p([^\>]*)><\/p>/', '', $html);
		if ($tagvs !== '') {
			$tagvspaces = $tagvs;
		}
		return $css.$html;
	}
	public static function isValidCSSSelectorForTag($dom, $key, $selector) {
		$valid = false; // value to be returned
		$tag = $dom[$key]['value'];
		$class = array();
		if (isset($dom[$key]['attribute']['class']) AND !empty($dom[$key]['attribute']['class'])) {
			$class = explode(' ', strtolower($dom[$key]['attribute']['class']));
		}
		$id = '';
		if (isset($dom[$key]['attribute']['id']) AND !empty($dom[$key]['attribute']['id'])) {
			$id = strtolower($dom[$key]['attribute']['id']);
		}
		$selector = preg_replace('/([\>\+\~\s]{1})([\.]{1})([^\>\+\~\s]*)/si', '\\1*.\\3', $selector);
		$matches = array();
		if (preg_match_all('/([\>\+\~\s]{1})([a-zA-Z0-9\*]+)([^\>\+\~\s]*)/si', $selector, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE) > 0) {
			$parentop = array_pop($matches[1]);
			$operator = $parentop[0];
			$offset = $parentop[1];
			$lasttag = array_pop($matches[2]);
			$lasttag = strtolower(trim($lasttag[0]));
			if (($lasttag == '*') OR ($lasttag == $tag)) {
				$attrib = array_pop($matches[3]);
				$attrib = strtolower(trim($attrib[0]));
				if (!empty($attrib)) {
					switch ($attrib[0]) {
						case '.': { // class
							if (in_array(substr($attrib, 1), $class)) {
								$valid = true;
							}
							break;
						}
						case '#': { // ID
							if (substr($attrib, 1) == $id) {
								$valid = true;
							}
							break;
						}
						case '[': { // attribute
							$attrmatch = array();
							if (preg_match('/\[([a-zA-Z0-9]*)[\s]*([\~\^\$\*\|\=]*)[\s]*["]?([^"\]]*)["]?\]/i', $attrib, $attrmatch) > 0) {
								$att = strtolower($attrmatch[1]);
								$val = $attrmatch[3];
								if (isset($dom[$key]['attribute'][$att])) {
									switch ($attrmatch[2]) {
										case '=': {
											if ($dom[$key]['attribute'][$att] == $val) {
												$valid = true;
											}
											break;
										}
										case '~=': {
											if (in_array($val, explode(' ', $dom[$key]['attribute'][$att]))) {
												$valid = true;
											}
											break;
										}
										case '^=': {
											if ($val == substr($dom[$key]['attribute'][$att], 0, strlen($val))) {
												$valid = true;
											}
											break;
										}
										case '$=': {
											if ($val == substr($dom[$key]['attribute'][$att], -strlen($val))) {
												$valid = true;
											}
											break;
										}
										case '*=': {
											if (strpos($dom[$key]['attribute'][$att], $val) !== false) {
												$valid = true;
											}
											break;
										}
										case '|=': {
											if ($dom[$key]['attribute'][$att] == $val) {
												$valid = true;
											} elseif (preg_match('/'.$val.'[\-]{1}/i', $dom[$key]['attribute'][$att]) > 0) {
												$valid = true;
											}
											break;
										}
										default: {
											$valid = true;
										}
									}
								}
							}
							break;
						}
						case ':': { // pseudo-class or pseudo-element
							if ($attrib[1] == ':') { // pseudo-element
							} else { // pseudo-class
							}
							break;
						}
					} // end of switch
				} else {
					$valid = true;
				}
				if ($valid AND ($offset > 0)) {
					$valid = false;
					$selector = substr($selector, 0, $offset);
					switch ($operator) {
						case ' ': { // descendant of an element
							while ($dom[$key]['parent'] > 0) {
								if (self::isValidCSSSelectorForTag($dom, $dom[$key]['parent'], $selector)) {
									$valid = true;
									break;
								} else {
									$key = $dom[$key]['parent'];
								}
							}
							break;
						}
						case '>': { // child of an element
							$valid = self::isValidCSSSelectorForTag($dom, $dom[$key]['parent'], $selector);
							break;
						}
						case '+': { // immediately preceded by an element
							for ($i = ($key - 1); $i > $dom[$key]['parent']; --$i) {
								if ($dom[$i]['tag'] AND $dom[$i]['opening']) {
									$valid = self::isValidCSSSelectorForTag($dom, $i, $selector);
									break;
								}
							}
							break;
						}
						case '~': { // preceded by an element
							for ($i = ($key - 1); $i > $dom[$key]['parent']; --$i) {
								if ($dom[$i]['tag'] AND $dom[$i]['opening']) {
									if (self::isValidCSSSelectorForTag($dom, $i, $selector)) {
										break;
									}
								}
							}
							break;
						}
					}
				}
			}
		}
		return $valid;
	}
	public static function getCSSdataArray($dom, $key, $css) {
		$cssarray = array(); // style to be returned
		$selectors = array();
		if (isset($dom[($dom[$key]['parent'])]['csssel'])) {
			$selectors = $dom[($dom[$key]['parent'])]['csssel'];
		}
		foreach($css as $selector => $style) {
			$pos = strpos($selector, ' ');
			$specificity = substr($selector, 0, $pos);
			$selector = substr($selector, $pos);
			if (self::isValidCSSSelectorForTag($dom, $key, $selector)) {
				if (!in_array($selector, $selectors)) {
					$cssarray[] = array('k' => $selector, 's' => $specificity, 'c' => $style);
					$selectors[] = $selector;
				}
			}
		}
		if (isset($dom[$key]['attribute']['style'])) {
			$cssarray[] = array('k' => '', 's' => '1000', 'c' => $dom[$key]['attribute']['style']);
		}
		$cssordered = array();
		foreach ($cssarray as $key => $val) {
			$skey = sprintf('%04d', $key);
			$cssordered[$val['s'].'_'.$skey] = $val;
		}
		ksort($cssordered, SORT_STRING);
		return array($selectors, $cssordered);
	}
	public static function getTagStyleFromCSSarray($css) {
		$tagstyle = ''; // value to be returned
		foreach ($css as $style) {
			$csscmds = explode(';', $style['c']);
			foreach ($csscmds as $cmd) {
				if (!empty($cmd)) {
					$pos = strpos($cmd, ':');
					if ($pos !== false) {
						$cmd = substr($cmd, 0, ($pos + 1));
						if (strpos($tagstyle, $cmd) !== false) {
							$tagstyle = preg_replace('/'.$cmd.'[^;]+/i', '', $tagstyle);
						}
					}
				}
			}
			$tagstyle .= ';'.$style['c'];
		}
		$tagstyle = preg_replace('/[;]+/', ';', $tagstyle);
		return $tagstyle;
	}
	public static function intToRoman($number) {
		$roman = '';
		while ($number >= 1000) {
			$roman .= 'M';
			$number -= 1000;
		}
		while ($number >= 900) {
			$roman .= 'CM';
			$number -= 900;
		}
		while ($number >= 500) {
			$roman .= 'D';
			$number -= 500;
		}
		while ($number >= 400) {
			$roman .= 'CD';
			$number -= 400;
		}
		while ($number >= 100) {
			$roman .= 'C';
			$number -= 100;
		}
		while ($number >= 90) {
			$roman .= 'XC';
			$number -= 90;
		}
		while ($number >= 50) {
			$roman .= 'L';
			$number -= 50;
		}
		while ($number >= 40) {
			$roman .= 'XL';
			$number -= 40;
		}
		while ($number >= 10) {
			$roman .= 'X';
			$number -= 10;
		}
		while ($number >= 9) {
			$roman .= 'IX';
			$number -= 9;
		}
		while ($number >= 5) {
			$roman .= 'V';
			$number -= 5;
		}
		while ($number >= 4) {
			$roman .= 'IV';
			$number -= 4;
		}
		while ($number >= 1) {
			$roman .= 'I';
			--$number;
		}
		return $roman;
	}
	public static function revstrpos($haystack, $needle, $offset = 0) {
		$length = strlen($haystack);
		$offset = ($offset > 0)?($length - $offset):abs($offset);
		$pos = strpos(strrev($haystack), strrev($needle), $offset);
		return ($pos === false)?false:($length - $pos - strlen($needle));
	}
	public static function getHyphenPatternsFromTEX($file) {
		$data = file_get_contents($file);
		$patterns = array();
		$data = preg_replace('/\%[^\n]*/', '', $data);
		preg_match('/\\\\patterns\{([^\}]*)\}/i', $data, $matches);
		$data = trim(substr($matches[0], 10, -1));
		$patterns_array = preg_split('/[\s]+/', $data);
		$patterns = array();
		foreach($patterns_array as $val) {
			if (!TCPDF_STATIC::empty_string($val)) {
				$val = trim($val);
				$val = str_replace('\'', '\\\'', $val);
				$key = preg_replace('/[0-9]+/', '', $val);
				$patterns[$key] = $val;
			}
		}
		return $patterns;
	}
	public static function getPathPaintOperator($style, $default='S') {
		$op = '';
		switch($style) {
			case 'S':
			case 'D': {
				$op = 'S';
				break;
			}
			case 's':
			case 'd': {
				$op = 's';
				break;
			}
			case 'f':
			case 'F': {
				$op = 'f';
				break;
			}
			case 'f*':
			case 'F*': {
				$op = 'f*';
				break;
			}
			case 'B':
			case 'FD':
			case 'DF': {
				$op = 'B';
				break;
			}
			case 'B*':
			case 'F*D':
			case 'DF*': {
				$op = 'B*';
				break;
			}
			case 'b':
			case 'fd':
			case 'df': {
				$op = 'b';
				break;
			}
			case 'b*':
			case 'f*d':
			case 'df*': {
				$op = 'b*';
				break;
			}
			case 'CNZ': {
				$op = 'W n';
				break;
			}
			case 'CEO': {
				$op = 'W* n';
				break;
			}
			case 'n': {
				$op = 'n';
				break;
			}
			default: {
				if (!empty($default)) {
					$op = self::getPathPaintOperator($default, '');
				} else {
					$op = '';
				}
			}
		}
		return $op;
	}
	public static function getTransformationMatrixProduct($ta, $tb) {
		$tm = array();
		$tm[0] = ($ta[0] * $tb[0]) + ($ta[2] * $tb[1]);
		$tm[1] = ($ta[1] * $tb[0]) + ($ta[3] * $tb[1]);
		$tm[2] = ($ta[0] * $tb[2]) + ($ta[2] * $tb[3]);
		$tm[3] = ($ta[1] * $tb[2]) + ($ta[3] * $tb[3]);
		$tm[4] = ($ta[0] * $tb[4]) + ($ta[2] * $tb[5]) + $ta[4];
		$tm[5] = ($ta[1] * $tb[4]) + ($ta[3] * $tb[5]) + $ta[5];
		return $tm;
	}
	public static function getSVGTransformMatrix($attribute) {
		$tm = array(1, 0, 0, 1, 0, 0);
		$transform = array();
		if (preg_match_all('/(matrix|translate|scale|rotate|skewX|skewY)[\s]*\(([^\)]+)\)/si', $attribute, $transform, PREG_SET_ORDER) > 0) {
			foreach ($transform as $key => $data) {
				if (!empty($data[2])) {
					$a = 1;
					$b = 0;
					$c = 0;
					$d = 1;
					$e = 0;
					$f = 0;
					$regs = array();
					switch ($data[1]) {
						case 'matrix': {
							if (preg_match('/([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
								$a = $regs[1];
								$b = $regs[2];
								$c = $regs[3];
								$d = $regs[4];
								$e = $regs[5];
								$f = $regs[6];
							}
							break;
						}
						case 'translate': {
							if (preg_match('/([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
								$e = $regs[1];
								$f = $regs[2];
							} elseif (preg_match('/([a-z0-9\-\.]+)/si', $data[2], $regs)) {
								$e = $regs[1];
							}
							break;
						}
						case 'scale': {
							if (preg_match('/([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
								$a = $regs[1];
								$d = $regs[2];
							} elseif (preg_match('/([a-z0-9\-\.]+)/si', $data[2], $regs)) {
								$a = $regs[1];
								$d = $a;
							}
							break;
						}
						case 'rotate': {
							if (preg_match('/([0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
								$ang = deg2rad($regs[1]);
								$x = $regs[2];
								$y = $regs[3];
								$a = cos($ang);
								$b = sin($ang);
								$c = -$b;
								$d = $a;
								$e = ($x * (1 - $a)) - ($y * $c);
								$f = ($y * (1 - $d)) - ($x * $b);
							} elseif (preg_match('/([0-9\-\.]+)/si', $data[2], $regs)) {
								$ang = deg2rad($regs[1]);
								$a = cos($ang);
								$b = sin($ang);
								$c = -$b;
								$d = $a;
								$e = 0;
								$f = 0;
							}
							break;
						}
						case 'skewX': {
							if (preg_match('/([0-9\-\.]+)/si', $data[2], $regs)) {
								$c = tan(deg2rad($regs[1]));
							}
							break;
						}
						case 'skewY': {
							if (preg_match('/([0-9\-\.]+)/si', $data[2], $regs)) {
								$b = tan(deg2rad($regs[1]));
							}
							break;
						}
					}
					$tm = self::getTransformationMatrixProduct($tm, array($a, $b, $c, $d, $e, $f));
				}
			}
		}
		return $tm;
	}
	public static function getVectorsAngle($x1, $y1, $x2, $y2) {
		$dprod = ($x1 * $x2) + ($y1 * $y2);
		$dist1 = sqrt(($x1 * $x1) + ($y1 * $y1));
		$dist2 = sqrt(($x2 * $x2) + ($y2 * $y2));
		$angle = acos($dprod / ($dist1 * $dist2));
		if (is_nan($angle)) {
			$angle = M_PI;
		}
		if ((($x1 * $y2) - ($x2 * $y1)) < 0) {
			$angle *= -1;
		}
		return $angle;
	}
	public static function pregSplit($pattern, $modifiers, $subject, $limit=NULL, $flags=NULL) {
		if ((strpos($modifiers, 'u') === FALSE) OR (count(preg_split('//u', "\n\t", -1, PREG_SPLIT_NO_EMPTY)) == 2)) {
			return preg_split($pattern.$modifiers, $subject, $limit, $flags);
		}
		$ret = array();
		while (($nl = strpos($subject, "\n")) !== FALSE) {
			$ret = array_merge($ret, preg_split($pattern.$modifiers, substr($subject, 0, $nl), $limit, $flags));
			$ret[] = "\n";
			$subject = substr($subject, ($nl + 1));
		}
		if (strlen($subject) > 0) {
			$ret = array_merge($ret, preg_split($pattern.$modifiers, $subject, $limit, $flags));
		}
		return $ret;
	}
	public static function fopenLocal($filename, $mode) {
		if (strpos($filename, '://') === false) {
			$filename = 'file://'.$filename;
		} elseif (stream_is_local($filename) !== true) {
			return false;
		}
		return fopen($filename, $mode);
	}
	public static function fileGetContents($file) {
		$alt = array($file);
		//
		if ((strlen($file) > 1)
		    && ($file[0] === '/')
		    && ($file[1] !== '/')
		    && !empty($_SERVER['DOCUMENT_ROOT'])
		    && ($_SERVER['DOCUMENT_ROOT'] !== '/')
		) {
		    $findroot = strpos($file, $_SERVER['DOCUMENT_ROOT']);
		    if (($findroot === false) || ($findroot > 1)) {
			$alt[] = htmlspecialchars_decode(urldecode($_SERVER['DOCUMENT_ROOT'].$file));
		    }
		}
		//
		$protocol = 'http';
		if (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
		    $protocol .= 's';
		}
		//
		$url = $file;
		if (preg_match('%^//%', $url) && !empty($_SERVER['HTTP_HOST'])) {
			$url = $protocol.':'.str_replace(' ', '%20', $url);
		}
		$url = htmlspecialchars_decode($url);
		$alt[] = $url;
		//
		if (preg_match('%^(https?)://%', $url)
		    && empty($_SERVER['HTTP_HOST'])
		    && empty($_SERVER['DOCUMENT_ROOT'])
		) {
			$urldata = parse_url($url);
			if (empty($urldata['query'])) {
				$host = $protocol.'://'.$_SERVER['HTTP_HOST'];
				if (strpos($url, $host) === 0) {
				    $tmp = str_replace($host, $_SERVER['DOCUMENT_ROOT'], $url);
				    $alt[] = htmlspecialchars_decode(urldecode($tmp));
				}
			}
		}
		//
		if (isset($_SERVER['SCRIPT_URI'])
		    && !preg_match('%^(https?|ftp)://%', $file)
		    && !preg_match('%^//%', $file)
		) {
		    $urldata = @parse_url($_SERVER['SCRIPT_URI']);
		    return $urldata['scheme'].'://'.$urldata['host'].(($file[0] == '/') ? '' : '/').$file;
		}
		//
		$alt = array_unique($alt);
		foreach ($alt as $path) {
			$ret = @file_get_contents($path);
			if ($ret !== false) {
			    return $ret;
			}
			if (!ini_get('allow_url_fopen')
				&& function_exists('curl_init')
				&& preg_match('%^(https?|ftp)://%', $path)
			) {
				$crs = curl_init();
				curl_setopt($crs, CURLOPT_URL, $path);
				curl_setopt($crs, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($crs, CURLOPT_FAILONERROR, true);
				curl_setopt($crs, CURLOPT_RETURNTRANSFER, true);
				if ((ini_get('open_basedir') == '') && (!ini_get('safe_mode'))) {
				    curl_setopt($crs, CURLOPT_FOLLOWLOCATION, true);
				}
				curl_setopt($crs, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($crs, CURLOPT_TIMEOUT, 30);
				curl_setopt($crs, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($crs, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($crs, CURLOPT_USERAGENT, 'tc-lib-file');
				$ret = curl_exec($crs);
				curl_close($crs);
				if ($ret !== false) {
					return $ret;
				}
			}
		}
		return false;
	}
} // END OF TCPDF_STATIC CLASS
